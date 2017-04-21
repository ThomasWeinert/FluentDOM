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

    /**
     * Add the current namespace configuration as xmlns* attributes to the element node.
     */
    public function applyNamespaces() {
      foreach ($this->_namespaces as $prefix => $namespaceUri) {
        $this->writeAttribute(
          empty($prefix) || $prefix == '#default' ? 'xmlns' : 'xmlns:'.$prefix, $namespaceUri
        );
      }
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
        parent::startElement(empty($prefix) ? $name : $prefix.':'.$name);
      } else {
        parent::startElementNS(empty($prefix) ? NULL : $prefix, $name, $namespaceUri);
        $this->_xmlnsStack->add($prefix, $namespaceUri);
      }
    }

    public function writeElementNS($prefix, $name, $uri, $content = NULL) {
      if ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        parent::writeElement(empty($prefix) ? $name : $prefix.':'.$name, $content);
      } else {
        parent::writeElementNS(empty($prefix) ? NULL : $prefix, $name, $uri, $content);
      }
    }

    public function endElement() {
      $this->_xmlnsStack->pop();
      parent::endElement();
    }

    public function startAttribute($name) {
      list($prefix, $localName) = QualifiedName::split($name);
      $this->startAttributeNS($prefix, $localName, $this->_namespaces->resolveNamespace($prefix));
    }

    public function writeAttribute($name, $value) {
      list($prefix, $localName) = QualifiedName::split($name);
      $this->writeAttributeNS($prefix, $localName, $this->_namespaces->resolveNamespace($prefix), $value);
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

    public function writeAttributeNS($prefix, $localName, $uri, $content) {
      if ((empty($prefix) && $localName == 'xmlns') || $prefix == 'xmlns') {
        $namespacePrefix = empty($prefix) ? '' : $localName;
        $namespaceUri = $content;
        if (!$this->_xmlnsStack->isDefined($namespacePrefix, $namespaceUri)) {
          parent::writeAttribute(empty($prefix) ? 'xmlns' : 'xmlns:'.$localName, $namespaceUri);
          $this->_xmlnsStack->add($namespacePrefix, $namespaceUri);
        }
      } elseif (empty($prefix)) {
        parent::writeAttribute($localName, $content);
      } elseif ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        parent::writeAttribute($prefix.':'.$localName, $content);
      } else {
        parent::writeAttributeNS($prefix, $localName, $uri, $content);
      }
    }
  }
}