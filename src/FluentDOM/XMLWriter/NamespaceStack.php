<?php

namespace FluentDOM\XMLWriter {

  class NamespaceStack {

    private $_stack = [];

    /**
     * @var NamespaceDefinition
     */
    private $_current;

    public function __construct() {
      $this->clear();
    }

    public function clear() {
      $this->_stack = [];
      $this->_current = new NamespaceDefinition();
    }

    public function push() {
      $this->_current->increaseDepth();
    }

    public function pop() {
      if ($this->_current->getDepth() < 1) {
        $this->_current = end($this->_stack);
      } else {
        $this->_current->decreaseDepth();
      }
    }

    public function isDefined($prefix, $namespaceUri) {
      return ($this->_current->resolveNamespace((string)$prefix) === $namespaceUri);
    }

    public function add($prefix, $namespaceUri) {
      if ($this->_current->getDepth() > 0) {
        $this->_stack[] = $this->_current;
        $this->_current = new NamespaceDefinition($this->_current);
      }
      $this->_current->registerNamespace($prefix, $namespaceUri);
    }
  }
}