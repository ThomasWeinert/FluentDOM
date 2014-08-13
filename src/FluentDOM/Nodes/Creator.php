<?php

namespace FluentDOM\Nodes {

  use FluentDOM\Appendable;
  use FluentDOM\Document;

  class Creator {

    private $_document = '';

    public function __construct(Document $document = NULL) {
      $this->_document = $document ?: new Document();
    }

    public function __isset($name) {
      switch ($name) {
      case 'formatOutput' :
        return isset($this->_document->{$name});
      }
    }

    public function __get($name) {
      switch ($name) {
      case 'formatOutput' :
        return $this->_document->{$name};
      }
    }

    public function __set($name, $value) {
      switch ($name) {
      case 'formatOutput' :
        $this->_document->{$name} = $value;
      }
    }

    public function registerNamespace($prefix, $namespaceUri) {
      $this->_document->registerNamespace($prefix, $namespaceUri);
    }

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

  class Node {

    private $_document = NULL;
    private $_node = NULL;

    public function __construct($document, \DOMNode $node) {
      $this->_document = $document;
      $this->_node = $node;
    }

    public function __get($name) {
      switch ($name) {
      case 'dom' :
      case 'document' :
        return $this->getDocument();
      case 'node' :
        return $this->_node;
      }
    }

    public function __set($name, $value) {
      throw new \LogicException(
        sprintf('%s is immutable.', get_class($this))
      );
    }

    public function getDocument() {
      $document = clone $this->_document;
      $document->appendChild($document->importNode($this->_node, TRUE));
      return $document;
    }

    public function __toString() {
      return $this->getDocument()->saveXml() ?: '';
    }
  }
}