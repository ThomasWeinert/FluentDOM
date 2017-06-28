<?php

namespace FluentDOM {

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

    /**
     * @param string $name
     * @return bool
     */
    public function startElement($name) {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespaceUri = $this->_namespaces->resolveNamespace((string)$prefix);
      return $this->startElementNS((string)$prefix, $localName, $namespaceUri);
    }

    /**
     * @param string $name
     * @param null|string $content
     * @return bool
     */
    public function writeElement($name, $content = NULL) {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespaceUri = $this->_namespaces->resolveNamespace((string)$prefix);
      return $this->writeElementNS((string)$prefix, $localName, $namespaceUri, $content);
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param string $namespaceUri
     * @return bool
     */
    public function startElementNS($prefix, $name, $namespaceUri) {
      $this->_xmlnsStack->push();
      if ($this->_xmlnsStack->isDefined($prefix, $namespaceUri)) {
        $result = parent::startElement(empty($prefix) ? $name : $prefix.':'.$name);
      } else {
        $result = parent::startElementNS(empty($prefix) ? NULL : $prefix, $name, $namespaceUri);
        $this->_xmlnsStack->add($prefix, $namespaceUri);
      }
      return $result;
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param string $uri
     * @param null|string $content
     * @return bool
     */
    public function writeElementNS($prefix, $name, $uri, $content = NULL) {
      if ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        return parent::writeElement(empty($prefix) ? $name : $prefix.':'.$name, $content);
      } else {
        return parent::writeElementNS(empty($prefix) ? NULL : $prefix, $name, $uri, $content);
      }
    }

    /**
     * @return bool
     */
    public function endElement() {
      $this->_xmlnsStack->pop();
      return parent::endElement();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function startAttribute($name) {
      list($prefix, $localName) = QualifiedName::split($name);
      return $this->startAttributeNS(
        (string)$prefix, $localName, $this->_namespaces->resolveNamespace((string)$prefix)
      );
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function writeAttribute($name, $value) {
      list($prefix, $localName) = QualifiedName::split($name);
      return $this->writeAttributeNS(
        (string)$prefix, $localName, $this->_namespaces->resolveNamespace((string)$prefix), $value
      );
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param string $uri
     * @return bool
     */
    public function startAttributeNS($prefix, $name, $uri) {
      if (empty($prefix)) {
        return parent::startAttribute($name);
      } elseif ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        return parent::startAttribute($prefix.':'.$name);
      } else {
        return parent::startAttributeNS($prefix, $name, $uri);
      }
    }

    /**
     * @param string $prefix
     * @param string $localName
     * @param string $uri
     * @param string $content
     * @return bool
     */
    public function writeAttributeNS($prefix, $localName, $uri, $content) {
      if ((empty($prefix) && $localName == 'xmlns') || $prefix == 'xmlns') {
        $namespacePrefix = empty($prefix) ? '' : $localName;
        $namespaceUri = $content;
        if (!$this->_xmlnsStack->isDefined($namespacePrefix, $namespaceUri)) {
          $result = parent::writeAttribute(empty($prefix) ? 'xmlns' : 'xmlns:'.$localName, $namespaceUri);
          $this->_xmlnsStack->add($namespacePrefix, $namespaceUri);
          return $result;
        } else {
          return false;
        }
      } elseif (empty($prefix)) {
        return parent::writeAttribute($localName, $content);
      } elseif ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        return parent::writeAttribute($prefix.':'.$localName, $content);
      } else {
        return parent::writeAttributeNS($prefix, $localName, $uri, $content);
      }
    }
  }
}