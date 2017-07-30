<?php
/**
 * Allow an object to be appendable to a FluentDOM\DOM\Element
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM {

  /**
   * @property bool $formatOutput
   * @property bool $optimizeNamespaces
   */
  class Creator {

    /**
     * @var DOM\Document
     */
    private $_document = NULL;

    /**
     * @var bool
     */
    private $_optimizeNamespaces = TRUE;

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct(string $version = '1.0', string $encoding = 'UTF-8') {
      $this->_document = new DOM\Document($version, $encoding);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool {
      switch ($name) {
      case 'formatOutput' :
        return isset($this->_document->{$name});
      case 'optimizeNamespaces' :
        return TRUE;
      }
      return FALSE;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
      switch ($name) {
      case 'formatOutput' :
        return $this->_document->{$name};
      case 'optimizeNamespaces' :
        return $this->_optimizeNamespaces;
      }
      return NULL;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) {
      switch ($name) {
      case 'formatOutput' :
        $this->_document->{$name} = $value;
        return;
      case 'optimizeNamespaces' :
        $this->_optimizeNamespaces = (bool)$value;
        return;
      }
      $this->{$name} = $value;
    }

    /**
     * If the creator is cloned, a clone of the dom document is needed, too.
     *
     */
    public function __clone() {
      $this->_document = clone $this->_document;
    }

    /**
     * @param string $prefix
     * @param string $namespaceURI
     */
    public function registerNamespace(string $prefix, string $namespaceURI) {
      $this->_document->registerNamespace($prefix, $namespaceURI);
    }

    /**
     * @param string $name
     * @param mixed ...$parameters
     * @return Creator\Node
     */
    public function __invoke(string $name, ...$parameters) {
      return new Creator\Node(
        $this,
        $this->_document,
        $this->element($name, ...$parameters)
      );
    }

    /**
     * Create an Element node and configure it.
     *
     * The first argument is the node name. All other arguments are flexible.
     *
     * - Arrays are set as attributes
     * - Attribute and Namespace nodes are set as attributes
     * - Nodes are appended as child nodes
     * - FluentDOM\Appendable instances are appended
     * - Strings or objects castable to string are appended as text nodes
     *
     * @param string $name
     * @param mixed ...$parameters
     * @return DOM\Element
     */
    public function element(string $name, ...$parameters): DOM\Element {
      $node = $this->_document->createElement($name);
      foreach ($parameters as $parameter) {
        $node->append($parameter);
      }
      return $node;
    }

    /**
     * @param string $content
     * @return DOM\CdataSection
     */
    public function cdata(string $content): DOM\CdataSection {
      return $this->_document->createCdataSection($content);
    }

    /**
     * @param string $content
     * @return DOM\Comment
     */
    public function comment($content): DOM\Comment {
      return $this->_document->createComment($content);
    }

    /**
     * @param string $target
     * @param string $content
     * @return DOM\ProcessingInstruction
     */
    public function pi($target, $content): DOM\ProcessingInstruction {
      return $this->_document->createProcessingInstruction($target, $content);
    }

    /**
     * @param array|\Traversable $traversable
     * @param callable $map
     * @return Appendable
     */
    public function each($traversable, callable $map = NULL): Appendable {
      return new Creator\Nodes($traversable, $map);
    }
  }
}

namespace FluentDOM\Creator {

  use FluentDOM\Appendable;
  use FluentDOM\Creator;
  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\Transformer\Namespaces\Optimize;

  /**
   * @property-read Document $document
   * @property-read Element $node
   */
  class Node implements Appendable, \IteratorAggregate {

    /**
     * @var Document
     */
    private $_document = NULL;

    /**
     * @var \DOMElement
     */
    private $_node = NULL;

    /**
     * @var Creator
     */
    private $_creator;

    /**
     * @param Creator $creator
     * @param Document $document
     * @param \DOMElement $node
     */
    public function __construct(Creator $creator, Document $document, \DOMElement $node) {
      $this->_creator = $creator;
      $this->_document = $document;
      $this->_node = $node;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
      switch ($name) {
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
    public function __set(string $name, $value) {
      throw new \LogicException(
        sprintf('%s is immutable.', get_class($this))
      );
    }

    /**
     * @return Document
     */
    public function getDocument(): Document {
      $document = clone $this->_document;
      $document->appendChild($document->importNode($this->_node, TRUE));
      if ($this->_creator->optimizeNamespaces) {
        $document = (new Optimize($document))->getDocument();
        $document->formatOutput = $this->_document->formatOutput;
      }
      return $document;
    }

    /**
     * @return string
     */
    public function __toString(): string {
      return $this->getDocument()->saveXml() ?: '';
    }

    /**
     * @param Element $parent
     * @return Element
     */
    public function appendTo(Element $parent): Element {
      $parent->appendChild(
        $parent->ownerDocument->importNode($this->_node, TRUE)
      );
      return $parent;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() {
      return new \ArrayIterator([$this->node]);
    }
  }

  class Nodes implements Appendable, \OuterIterator {

    /**
     * @var array|\Traversable
     */
    private $_traversable = NULL;

    /**
     * @var callable|NULL
     */
    private $_map = NULL;

    /**
     * @var NULL|\Iterator
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
    public function getInnerIterator(): \Iterator {
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
    public function valid(): bool {
      return $this->getInnerIterator()->valid();
    }

    /**
     * @param Element $parent
     * @return Element
     */
    public function appendTo(Element $parent): Element {
      foreach ($this as $item) {
        $parent->append($item);
      }
      return $parent;
    }
  }
}