<?php

namespace FluentDOM\Nodes {

  use FluentDOM\Appendable;
  use FluentDOM\CdataSection;
  use FluentDOM\Document;
  use FluentDOM\Nodes\Creator\Nodes;

  /**
   * @property bool $formatOutput
   */
  class Creator {

    /**
     * @var Document
     */
    private $_document = NULL;

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct($version = '1.0', $encoding = 'UTF-8') {
      $this->_document = new Document($version, $encoding);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
      switch ($name) {
      case 'formatOutput' :
        return isset($this->_document->{$name});
      }
      return FALSE;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
      switch ($name) {
      case 'formatOutput' :
        return $this->_document->{$name};
      }
      return NULL;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
      switch ($name) {
      case 'formatOutput' :
        $this->_document->{$name} = $value;
      }
      $this->{$name} = $value;
    }

    /**
     * @param string $prefix
     * @param string $namespaceUri
     */
    public function registerNamespace($prefix, $namespaceUri) {
      $this->_document->registerNamespace($prefix, $namespaceUri);
    }

    /**
     * @param string $name
     * @param mixed ...$parameter
     * @return Creator\Node
     */
    public function __invoke($name) {
      return new Creator\Node(
        $this->_document,
        call_user_func_array(
          array($this, 'element'), func_get_args()
        )
      );
    }

    /**
     * Create an Element node and configure it.
     *
     * The first argument is the node name. All other arguments are flexible.
     *
     * - Arrays are set as attributes
     * - Attribute and Namesspace nodes are set as attributes
     * - Nodes are appended as child nodes
     * - FluentDOM\Appendable instances are appended
     * - Strings or objects castable to string are appended as text nodes
     *
     * @param string $name
     * @param mixed ...$parameter
     * @return \FluentDOM\Element
     */
    public function element($name) {
      $node = $this->_document->createElement($name);
      $arguments = func_get_args();
      array_shift($arguments);
      foreach ($arguments as $parameter) {
        $this->addToNode($node, $parameter);
      }
      return $node;
    }

    /**
     * @param \FluentDOM\Element $node
     * @param mixed $item
     */
    private function addToNode($node, $item) {
      if (is_array($item)) {
        foreach ($item as $name => $value) {
          if (is_scalar($value)) {
            $node->setAttribute($name, $value);
          }
        }
      } elseif ($item instanceof Appendable) {
        $node->append($item);
      } elseif ($item instanceof \DOMAttr) {
        $node->setAttributeNode($this->_document->importNode($item));
      } elseif ($item instanceof \DOMNode) {
        $node->appendChild($this->_document->importNode($item));
      } elseif ($item instanceof Creator\Node) {
        $node->appendChild($item->node);
      } elseif ($item instanceof Creator\Nodes) {
        foreach ($item as $childItem) {
          $this->addToNode($node, $childItem);
        }
      } elseif (is_string($item) || method_exists($item, '__toString')) {
        $node->appendChild(
          $this->_document->createTextNode($item)
        );
      }
    }

    /**
     * @param string $content
     * @return \FluentDOM\CdataSection
     */
    public function cdata($content) {
      return $this->_document->createCDATASection($content);
    }

    /**
     * @param string $content
     * @return \FluentDOM\Comment
     */
    public function comment($content) {
      return $this->_document->createComment($content);
    }

    /**
     * @param string $target
     * @param string $content
     * @return \DOMProcessingInstruction
     */
    public function pi($target, $content) {
      return $this->_document->createProcessingInstruction($target, $content);
    }

    /**
     * @param array|\Traversable $traversable
     * @param callable $map
     * @return \Traversable
     */
    public function any($traversable, callable $map = NULL) {
      return new Nodes($traversable, $map);
    }
  }
}

namespace FluentDOM\Nodes\Creator {

  use FluentDOM\Document;

  /**
   * @property-read Document $document
   * @property-read Document $dom
   * @property-read \DOMElement $node
   */
  class Node {

    /**
     * @var Document
     */
    private $_document = NULL;

    /**
     * @var \DOMElement
     */
    private $_node = NULL;

    /**
     * @param Document $document
     * @param \DOMElement $node
     */
    public function __construct($document, \DOMElement $node) {
      $this->_document = $document;
      $this->_node = $node;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
      switch ($name) {
      case 'dom' :
      case 'document' :
        return $this->getDocument();
      case 'node' :
        return $this->_node;
      }
      return NULL;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \LogicException
     */
    public function __set($name, $value) {
      throw new \LogicException(
        sprintf('%s is immutable.', get_class($this))
      );
    }

    /**
     * @return Document
     */
    public function getDocument() {
      $document = clone $this->_document;
      $document->appendChild($document->importNode($this->_node, TRUE));
      return $document;
    }

    /**
     * @return string
     */
    public function __toString() {
      return $this->getDocument()->saveXml() ?: '';
    }
  }

  class Nodes implements \OuterIterator {

    /**
     * @var array|\Traversable
     */
    private $_traversable = NULL;

    /**
     * @var callable|null
     */
    private $_map = NULL;

    /**
     * @var null|\Iterator
     */
    private $_iterator = NULL;

    /**
     * @param array|\Traversable $traversable
     * @param callable $map
     */
    public function __construct($traversable, callable $map = NULL) {
      $this->_traversable = $traversable;
      $this->_map = $map;
    }

    /**
     * @return \Iterator
     */
    public function getInnerIterator() {
      if (NULL === $this->_iterator) {
        if ($this->_traversable instanceof \Iterator) {
          $this->_iterator = $this->_traversable;
        } elseif (is_array($this->_traversable)) {
          $this->_iterator = new \ArrayIterator($this->_traversable);
        } else {
          $this->_iterator = ($this->_traversable instanceof \Traversable)
            ? new \IteratorIterator($this->_traversable)
            : new \EmptyIterator();
        }
      }
      return $this->_iterator;
    }

    public function rewind() {
      $this->getInnerIterator()->rewind();
    }

    public function next() {
      $this->getInnerIterator()->next();
    }

    /**
     * @return string|int|float
     */
    public function key() {
      return $this->getInnerIterator()->key();
    }

    /**
     * @return mixed
     */
    public function current() {
      if (isset($this->_map)) {
        return call_user_func(
          $this->_map,
          $this->getInnerIterator()->current(),
          $this->getInnerIterator()->key()
        );
      }
      return $this->getInnerIterator()->current();
    }

    /**
     * @return bool
     */
    public function valid() {
      return $this->getInnerIterator()->valid();
    }
  }
}