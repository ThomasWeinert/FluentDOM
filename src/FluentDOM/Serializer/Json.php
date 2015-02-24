<?php
/**
 * Serialize a DOM into a Json structure.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer {
  use FluentDOM\Xpath;

  /**
   * Serialize a DOM into a Json structure. This loader allows to save an imported Json back as JSON.
   *
   * Using this on a standard XML document will ignore a lot of data. Namespaces and Attributes
   * are ignored, if here are two elements with the same name only the last will be in the output.
   * If an element has child elements, all text child nodes will be ignored.
   *
   * See the other serializers, to keep this data.
   *
   * This serializer recognizes attributes from the JsonDOM namespaces. If you import an JSON to a DOM
   * in FluentDOM, the additional information is stored in these attributes (types, names, ...)
   *
   * Here is a example of an XML:
   *
   * <?xml version="1.0" encoding="UTF-8"?>
   * <json:json xmlns:json="urn:carica-json-dom.2013">
   *   <boolean json:type="boolean">true</boolean>
   *   <int json:type="number">42</int>
   *   <null json:type="null"/>
   *   <string>Foo</string>
   *   <array json:type="array">
   *     <_ json:type="number">21</_>
   *   </array>
   *   <acomplexname json:type="object" json:name="a complex name"/>
   * </json:json>
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  class Json implements \JsonSerializable {

    const XMLNS_JSONDOM = 'urn:carica-json-dom.2013';

    /**
     * @var \DOMDocument
     */
    protected $_document = NULL;

    /**
     * @var int
     */
    private $_options = 0;

    /**
     * @var int
     */
    private $_depth = 512;

    /**
     * Allow the use of the recursion limitation argument
     * @var bool
     */
    private $_useDepth = FALSE;

    /**
     * @param \DOMDocument $document
     * @param int $options
     * @param int $depth
     */
    public function __construct(\DOMDocument $document, $options = 0, $depth = 512) {
      $this->_document = $document;
      $this->_options = (int)$options;
      $this->_depth = (int)$depth;
      $this->_useDepth = \FluentDOM::$isHHVM || version_compare(PHP_VERSION, '5.5.0', '>=');
    }

    /**
     * @return string
     */
    public function __toString() {
      $json = $this->_useDepth
        ? json_encode($this, $this->_options, $this->_depth)
        : json_encode($this, $this->_options);
      return ($json) ? $json : '';
    }

    /**
     * @return mixed
     */
    public function jsonSerialize() {
      if (isset($this->_document->documentElement)) {
        return $this->getNode($this->_document->documentElement);
      }
      return $this->getEmpty();
    }

    /**
     * @param \DOMElement $node
     * @return mixed
     */
    protected function getNode(\DOMElement $node) {
      switch ($this->getType($node)) {
      case 'object' :
        $result = new \stdClass();
        /** @var \DOMElement $child */
        foreach ($this->getChildElements($node) as $child) {
          $result->{$this->getName($child)} = $this->getNode($child);
        }
        break;
      case 'array' :
        $result = [];
        foreach ($this->getChildElements($node) as $child) {
          $result[] = $this->getNode($child);
        }
        break;
      case 'number' :
        return (float)$node->nodeValue;
      case 'boolean' :
        return $node->nodeValue === 'true' ? true : false;
      case 'null' :
        return null;
      default :
        return $node->nodeValue;
      }
      return $result;
    }

    /**
     * @param \DOMElement $source
     * @return \DOMNodeList
     */
    private function getChildElements(\DOMElement $source) {
      $xpath = new Xpath($source->ownerDocument);
      return $xpath('*', $source);
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    private function getType(\DOMElement $node) {
      if ($node->hasAttributeNS(self::XMLNS_JSONDOM, 'type')) {
        return $node->getAttributeNS(self::XMLNS_JSONDOM, 'type');
      } else {
        $xpath = new Xpath($node->ownerDocument);
        return $xpath('count(*) > 0', $node) ? 'object' : 'string';
      }
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    private function getName(\DOMElement $node) {
      if ($node->hasAttributeNS(self::XMLNS_JSONDOM, 'name')) {
        return $node->getAttributeNS(self::XMLNS_JSONDOM, 'name');
      } else {
        return $node->localName;
      }
    }

    /**
     * @return mixed
     */
    protected function getEmpty() {
      return new \stdClass();
    }

    /**
     * Get the namespace definitions needed for this node.
     *
     * If compares the namespaces of the current node with the ones from
     * the parent node. Only definitions that are different are returned.
     *
     * @param \DOMElement $node
     * @return array
     */
    protected function getNamespaces(\DOMElement $node) {
      $result = $this->getAllNamespaces($node);
      $inherited = [];
      if ($node->parentNode instanceOf \DOMElement) {
        $inherited = $this->getAllNamespaces($node->parentNode);
      }
      return array_diff_assoc($result,$inherited);
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    private function getAllNamespaces(\DOMElement $node) {
      $xpath = new Xpath($node->ownerDocument);
      $result = [];
      foreach ($xpath->evaluate('namespace::*', $node) as $namespace) {
        if (
          ($namespace->nodeName !== 'xmlns:xml') &&
          ($namespace->nodeName !== 'xmlns:xmlns')
        ) {
          $result[$namespace->nodeName] = $namespace->namespaceURI;
        }
      };
      return $result;
    }
  }
}