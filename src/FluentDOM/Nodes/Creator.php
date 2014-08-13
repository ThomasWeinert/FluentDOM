<?php

namespace FluentDOM\Nodes {

  use FluentDOM\Appendable;
  use FluentDOM\Document;

  /**
   * @property bool $formatOutput
   */
  class Creator {

    /**
     * @var Document
     */
    private $_document = NULL;

    /**
     * @param Document $document
     */
    public function __construct(Document $document = NULL) {
      $this->_document = $document ?: new Document();
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
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
      switch ($name) {
      case 'formatOutput' :
        $this->_document->{$name} = $value;
      }
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
      $node = $this->_document->createElement($name);
      $arguments = func_get_args();
      array_shift($arguments);
      foreach ($arguments as $parameter) {
        if (is_array($parameter)) {
          foreach ($parameter as $name => $value) {
            $node->setAttribute($name, $value);
          }
        } elseif ($parameter instanceof Appendable) {
          $node->append($parameter);
        } elseif ($parameter instanceof \DOMNode) {
          $node->appendChild($this->_document->importNode($parameter));
        } elseif ($parameter instanceof Creator\Node) {
          $node->appendChild($parameter->node);
        } elseif (is_string($parameter) || method_exists($parameter, '__toString')) {
          $node->appendChild(
            $this->_document->createTextNode($parameter)
          );
        }
      }
      return new Creator\Node($this->_document, $node);
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
}