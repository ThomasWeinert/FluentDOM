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
   * @method Attribute createAttributeNS($namespace, $name)
   * @method CdataSection createCdataSection($data)
   * @method Comment createComment($data)
   * @method DocumentFragment createDocumentFragment()
   * @method ProcessingInstruction createProcessingInstruction($target, $data)
   * @method Text createTextNode($content)
   *
   * @property-read Element $documentElement
   * @property-read Element $firstElementChild
   * @property-read Element $lastElementChild
   */
  class Document extends \DOMDocument implements Node\ParentNode {

    use
      Node\ParentNode\Properties,
      Node\QuerySelector\Implementation,
      Node\Xpath;

    /**
     * @var Xpath
     */
    private $_xpath = NULL;

    /**
     * @var array
     */
    private $_namespaces = [];

    /**
     * @var array
     */
    private $_reserved = [
      'xml' => 'http://www.w3.org/XML/1998/namespace',
      'xmlns' => 'http://www.w3.org/2000/xmlns/'
    ];

    /**
     * Map dom node classes to extended descendants.
     *
     * @var array
     */
    private $_classes = [
      'DOMDocument' => '\\Document',
      'DOMAttr' => '\\Attribute',
      'DOMCdataSection'=> '\\CdataSection',
      'DOMComment'=> '\\Comment',
      'DOMElement'=> '\\Element',
      'DOMProcessingInstruction'=> '\\ProcessingInstruction',
      'DOMText'=> '\\Text',
      'DOMDocumentFragment'=> '\\DocumentFragment'
    ];

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct($version = '1.0', $encoding = 'UTF-8') {
      parent::__construct($version, $encoding);
      foreach ($this->_classes as $superClass => $className) {
        $this->registerNodeClass($superClass, __NAMESPACE__.$className);
      }
    }

    /**
     * Generate an xpath instance for the document, if the document of the
     * xpath instance does not match the document, regenerate it.
     *
     * @return Xpath
     */
    public function xpath() {
      if (
        isset($this->_xpath) &&
        (\FluentDOM::$isHHVM || $this->_xpath->document === $this)
      ) {
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
     * @throws \LogicException
     */
    public function registerNamespace($prefix, $namespace) {
      $prefix = $this->validatePrefix($prefix);
      if (isset($this->_reserved[$prefix])) {
        throw new \LogicException(
          sprintf('Can not register reserved namespace prefix "%s".', $prefix)
        );
      }
      $this->_namespaces[$prefix] = $namespace;
      if (isset($this->_xpath) && $prefix !== '#default') {
        $this->_xpath->registerNamespace($prefix, $namespace);
      }
    }

    /**
     * Get the namespace for a given prefix
     *
     * @param string $prefix
     * @throws \LogicException
     * @return string
     */
    public function getNamespace($prefix) {
      $prefix = $this->validatePrefix($prefix);
      if (isset($this->_reserved[$prefix])) {
        return $this->_reserved[$prefix];
      }
      if (isset($this->_namespaces[$prefix])) {
        return $this->_namespaces[$prefix];
      }
      if ($prefix === '#default') {
        return '';
      }
      throw new \LogicException(
        sprintf('Unknown namespace prefix "%s".', $prefix)
      );
    }

    /**
     * Get set the namespaces registered for the document object.
     *
     * If the argument is provided ALL namespaces will be replaced.
     *
     * @param array $namespaces
     * @return array
     */
    public function namespaces(array $namespaces = NULL) {
      if (isset($namespaces)) {
        $this->_namespaces = [];
        foreach($namespaces as $prefix => $namespaceUri) {
          $this->registerNamespace($prefix, $namespaceUri);
        }
      }
      return $this->_namespaces;
    }

    /**
     * @param string $prefix
     * @return string
     */
    private function validatePrefix($prefix) {
      return empty($prefix) ? '#default' : $prefix;
    }

    /**
     * If here is a ':' in the element name, consider it a namespace prefix
     * registered on the document.
     *
     * Allow to add a text content and attributes directly.
     *
     * If $content is an array, the $content argument  will be merged with the $attributes
     * argument.
     *
     * @param string $name
     * @param string|array $content
     * @param array $attributes
     * @throws \LogicException
     * @return Element
     */
    public function createElement($name, $content = NULL, array $attributes = NULL) {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespace = '';
      if ($prefix !== FALSE) {
        if (empty($prefix)) {
          $name = $localName;
        } else {
          if (isset($this->_reserved[$prefix])) {
            throw new \LogicException(
              sprintf('Can not use reserved namespace prefix "%s" in element name.', $prefix)
            );
          }
          $namespace = $this->getNamespace($prefix);
        }
      } else {
        $namespace = $this->getNamespace('#default');
      }
      if ($namespace != '') {
        $node = $this->createElementNS($namespace, $name);
      } elseif (isset($this->_namespaces['#default'])) {
        $node = $this->createElementNS('', $name);
      } else {
        $node = parent::createElement($name);
      }
      $this->appendAttributes($node, $content, $attributes);
      $this->appendContent($node, $content);
      return $node;
    }

    /**
     * @param string $namespaceURI
     * @param string $qualifiedName
     * @param string|null $content
     * @return Element
     */
    public function createElementNS($namespaceURI, $qualifiedName, $content = null) {
      $node = parent::createElementNS($namespaceURI, $qualifiedName);
      $this->appendContent($node, $content);
      return $node;
    }

    /**
     * If here is a ':' in the attribute name, consider it a namespace prefix
     * registered on the document.
     *
     * Allow to add a attribute value directly.
     *
     * @param string $name
     * @param string|null $value
     * @return \DOMAttr
     */
    public function createAttribute($name, $value = NULL) {
      list($prefix) = QualifiedName::split($name);
      if (empty($prefix)) {
        $node = parent::createAttribute($name);
      } else {
        $node = $this->createAttributeNS($this->getNamespace($prefix), $name);
      }
      if (isset($value)) {
        $node->value = $value;
      }
      return $node;
    }

    /**
     * Overload appendElement to add a text content and attributes directly.
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return Element
     */
    public function appendElement($name, $content = '', array $attributes = NULL) {
      $this->appendChild(
        $node = $this->createElement($name, $content, $attributes)
      );
      return $node;
    }

    /**
     * Put the document node into a FluentDOM\Query
     * and call find() on it.
     *
     * @param string $expression
     * @return Query
     */
    public function find($expression) {
      return \FluentDOM::Query($this)->find($expression);
    }

    /**
     * @param \DOMElement $node
     * @param string|array|NULL $content
     * @param array|NULL $attributes
     */
    private function appendAttributes($node, $content = NULL, array $attributes = NULL) {
      if (is_array($content)) {
        $attributes = NULL === $attributes
          ? $content : array_merge($content, $attributes);
      }
      if (!empty($attributes)) {
        foreach ($attributes as $attributeName => $attributeValue) {
          $node->setAttribute($attributeName, $attributeValue);
        }
      }
    }

    /**
     * @param \DOMElement $node
     * @param string|array|NULL $content
     */
    private function appendContent($node, $content = NULL) {
      if (!((empty($content) && !is_numeric($content)) || is_array($content) )) {
        $node->appendChild($this->createTextNode($content));
      }
    }

    /**
     * Allow to save XML fragments, providing a node list
     *
     * Overloading saveXML() with a removed type hint triggers an E_STRICT error,
     * so we the function needs a new name. :-(
     *
     * @param \DOMNode|\DOMNodeList|NULL $context
     * @param int $options
     * @return string
     */
    public function toXml($context = NULL, $options = 0) {
      if ($context instanceof \DOMNodeList) {
        $result = '';
        foreach ($context as $node) {
          $result .= $this->saveXML($node, $options);
        }
        return $result;
      }
      return $this->saveXML($context, $options);
    }

    /**
     * Allow to save HTML fragments, providing a node list.
     *
     * This is an alias for the extended saveHTML() method. Make it
     * consistent with toXml()
     *
     * @param \DOMNode|\DOMNodeList|NULL $context
     * @return string
     */
    public function toHtml($context = NULL) {
      return $this->saveHtml($context);
    }

    /**
     * Allow to save HTML fragments, providing a node list
     *
     * @param \DOMNode|\DOMNodeList|NULL $context
     * @return string
     */
    public function saveHTML($context = NULL) {
      if ($context instanceof \DOMNodeList) {
        $result = '';
        foreach ($context as $node) {
          $result .= parent::saveHTML($node);
        }
        return $result;
      }
      return parent::saveHTML($context);
    }

    /**
     * Allow getElementsByTagName to use the defined namespaces.
     *
     * @param string $name
     * @return \DOMNodeList
     */
    public function getElementsByTagName($name) {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespace = $namespace = $this->getNamespace((string)$prefix);
      if ($namespace != '') {
        return $this->getElementsByTagNameNS($namespace, $localName);
      } else {
        return parent::getElementsByTagName($localName);
      }
    }
  }
}
