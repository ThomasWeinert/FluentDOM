<?php
/**
 * FluentDOM\Document extends PHPs DOMDocument class. It adds some generic namespace handling on
 * the document level and registers extended Node classes for convenience.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * @property Element $document
   */
  class Document extends \DOMDocument {

    /**
     * @var Xpath
     */
    private $_xpath = NULL;

    /**
     * @var array
     */
    private $_namespaces = [];

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct($version = '1.0', $encoding = 'UTF-8') {
      parent::__construct($version, $encoding);
      $this->registerNodeClass('DOMElement', __NAMESPACE__.'\\Element');
    }

    /**
     * Generate an xpath instance for the document, if the document of the
     * xpath instance does not match the document, regenerate it.
     *
     * @return Xpath
     */
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

    /**
     * register a namespace prefix for the document, it will be used in
     * createElement and setAttribute
     *
     * @param string $prefix
     * @param string $namespace
     */
    public function registerNamespace($prefix, $namespace) {
      $this->_namespaces[$prefix] = $namespace;
      if (isset($this->_xpath)) {
        $this->_xpath->registerNamespace($prefix, $namespace);
      }
    }

    /**
     * Get the namespace for a given prefix
     *
     * @param string $prefix
     * @return null
     */
    public function getNamespace($prefix) {
      if (isset($this->_namespaces[$prefix])) {
        return $this->_namespaces[$prefix];
      }
      return NULL;
    }

    /**
     * If here is a ':' in the element name, consider it a namespace prefix
     * registered on the document.
     *
     * Allow to add a text content and attributes directly.
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return \DOMElement
     */
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

    /**
     * Overload appendElement to add a text content and attributes directly.
     *
     * @param $name
     * @param string $content
     * @param array $attributes
     * @return \DOMElement
     */
    public function appendElement($name, $content = '', array $attributes = NULL) {
      $this->appendChild(
        $node = $this->createElement($name, $content, $attributes)
      );
      return $node;
    }
  }
}