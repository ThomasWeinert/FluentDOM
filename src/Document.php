<?php

namespace FluentDOM {

  class Document extends \DOMDocument {

    /**
     * @var Xpath
     */
    private $_xpath = NULL;

    /**
     * @var array
     */
    private $_namespaces = [];

    public function __construct($version = '1.0', $encoding = 'UTF-8') {
      parent::__construct($version, $encoding);
      $this->registerNodeClass('DOMElement', __NAMESPACE__.'\\Element');
    }

    public function xpath() {
      if (isset($this->_xpath) && $this->_xpath->document == $this) {
        return $this->_xpath;
      }
      $this->_xpath = new Xpath($this);
      foreach ($this->_namespaces as $prefix => $namespace) {
        $this->_xpath->registerNamespace($prefix, $namespace);
      }
      return $this->_xpath;
    }

    public function registerNamespace($prefix, $namespace) {
      $this->_namespaces[$prefix] = $namespace;
      if (isset($this->_xpath)) {
        $this->_xpath->registerNamespace($prefix, $namespace);
      }
    }

    public function getNamespace($prefix) {
      if (isset($this->_namespaces[$prefix])) {
        return $this->_namespaces[$prefix];
      }
      return NULL;
    }

    public function createElement($name, $content = NULL, array $attributes = array()) {
      if (FALSE !== ($position = strpos($name, ':'))) {
        $node = parent::createElementNS(
          $this->getNamespace(substr($name, 0, $position)),
          $name
        );
      } else {
        $node = parent::createElement($name);
      }
      if (!empty($attributes)) {
        foreach ($attributes as $attributeName => $attributeValue) {
          $node->setAttribute($attributeName, $attributeValue);
        }
      }
      if (!empty($content)) {
        $node->appendChild($this->createTextNode($content));
      }
      return $node;
    }
  }

}