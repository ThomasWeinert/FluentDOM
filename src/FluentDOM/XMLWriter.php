<?php

namespace FluentDOM {

  use FluentDOM\XMLWriter\NamespaceDefinition;
  use FluentDOM\XMLWriter\NamespaceStack;

  class XMLWriter extends \XMLWriter {

    /**
     * @var Namespaces
     */
    private $_namespaces;

    /**
     * @var XMLWriter\NamespaceStack
     */
    private $_xmlnsStack;

    public function __construct() {
      $this->_namespaces = new Namespaces();
      $this->_xmlnsStack = new XMLWriter\NamespaceStack();
    }

    /**
     * register a namespace prefix for the xml reader, it will be used in
     * next() and other methods with a tag name argument
     *
     * @param string $prefix
     * @param string $namespace
     * @throws \LogicException
     */
    public function registerNamespace($prefix, $namespace) {
      $this->_namespaces[$prefix] = $namespace;
    }

    public function startElement($name) {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespaceUri = $this->_namespaces->resolveNamespace($prefix);
      return $this->startElementNS((string)$prefix, $localName, $namespaceUri);
    }

    public function writeElement($name, $content = NULL) {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespaceUri = $this->_namespaces->resolveNamespace($prefix);
      return $this->writeElementNS((string)$prefix, $localName, $namespaceUri, $content);
    }

    public function startElementNS($prefix, $name, $namespaceUri) {
      $this->_xmlnsStack->push();
      if ($this->_xmlnsStack->isDefined($prefix, $namespaceUri)) {
        parent::startElement(
          empty($prefix) ? $name : $prefix.':'.$name
        );
      } else {
        parent::startElementNS($prefix, $name, $namespaceUri);
        $this->_xmlnsStack->add($prefix, $namespaceUri);
      }
    }

    public function writeElementNS($prefix, $name, $uri, $content = NULL) {
      if ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        parent::writeElement(
          empty($prefix) ? $name : $prefix.':'.$name, $content
        );
      } else {
        parent::writeElementNS($prefix, $name, $uri, $content);
      }
    }

    public function endElement() {
      $this->_xmlnsStack->pop();
      parent::endElement();
    }

    public function startAttribute($name) {
      list($prefix) = QualifiedName::split($name);
      $this->startAttributeNS($prefix, $name, $this->_namespaces->resolveNamespace($prefix));
    }

    public function writeAttribute($name, $value) {
      list($prefix) = QualifiedName::split($name);
      $this->writeAttributeNS($prefix, $name, $this->_namespaces->resolveNamespace($prefix), $value);
    }

    public function startAttributeNS($prefix, $name, $uri) {
      if (empty($prefix)) {
        parent::startAttribute($name);
      } elseif ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        parent::startAttribute($prefix.':'.$name);
      } else {
        parent::startAttributeNS($prefix, $name, $uri);
      }
    }

    public function writeAttributeNS($prefix, $name, $uri, $content) {
      if (empty($prefix)) {
        parent::writeAttribute($name, $content);
      } elseif ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        parent::writeAttribute($prefix.':'.$name, $content);
      } else {
        parent::writeAttributeNS($prefix, $name, $uri, $content);
      }
    }
  }
}

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

  class NamespaceDefinition {

    /**
     * @var int
     */
    private $_indent;
    /**
     * @var \FluentDOM\Namespaces
     */
    private $_namespaces;

    public function __construct($inherit = NULL) {
      $this->_indent = 0;
      $this->_namespaces = new \FluentDOM\Namespaces($inherit);
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

    public function registerNamespace($prefix, $namespaceUri) {
      $this->_namespaces[$prefix] = $namespaceUri;
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