<?php

namespace FluentDOM\XMLWriter {

  use FluentDOM\Utility\Namespaces;

  class NamespaceDefinition {

    /**
     * @var int
     */
    private $_indent;
    /**
     * @var Namespaces
     */
    private $_namespaces;

    public function __construct($inherit = NULL) {
      $this->_indent = 0;
      $this->_namespaces = new Namespaces($inherit);
    }

    public function getDepth() {
      return $this->_indent;
    }

    public function increaseDepth() {
      return ++$this->_indent;
    }

    public function decreaseDepth() {
      if ($this->_indent > 0) {
        return --$this->_indent;
      }
      throw new \LogicException('Did not resolve namespace levels properly.');
    }

    public function registerNamespace($prefix, $namespaceURI) {
      $this->_namespaces[$prefix] = $namespaceURI;
    }

    public function resolveNamespace($prefix) {
      try {
        return $this->_namespaces->resolveNamespace($prefix);
      } catch (\LogicException $e) {
        return '';
      }
    }
  }
}