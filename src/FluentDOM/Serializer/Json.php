<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Serializer {

  use FluentDOM\DOM\Xpath;
  use FluentDOM\Loader\Json\JsonDOM;
  use FluentDOM\Utility\StringCastable;

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
   *   <a-complex-name json:type="object" json:name="a complex name"/>
   * </json:json>
   */
  class Json implements \JsonSerializable, StringCastable {

    private const XMLNS_JSONDOM = JsonDOM::XMLNS;

    /**
     * @var \DOMNode
     */
    protected $_node;

    /**
     * @var int
     */
    private $_options;

    /**
     * @var int
     */
    private $_depth;

    /**
     * @param \DOMNode $node
     * @param int $options
     * @param int $depth
     */
    public function __construct(\DOMNode $node, int $options = 0, int $depth = 512) {
      $this->_node = $node;
      $this->_options = $options;
      $this->_depth = $depth;
    }

    /**
     * @return string
     */
    public function __toString(): string {
      $json = (string)json_encode($this, $this->_options, $this->_depth);
      return $json ?: '';
    }

    /**
     * @return mixed
     */
    public function jsonSerialize() {
      $node = $this->_node;
      if ($node instanceof \DOMDocument) {
        $node = $node->documentElement;
      }
      if ($node instanceof \DOMElement) {
        return $this->getNode($node);
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
          $key = $this->getKey($child);
          $result->{$key} = $this->getNode($child);
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
        return $node->nodeValue === 'true';
      case 'null' :
        return NULL;
      default :
        return $node->nodeValue;
      }
      return $result;
    }

    /**
     * @param \DOMElement $source
     * @return \DOMNodeList
     */
    private function getChildElements(\DOMElement $source): \DOMNodeList {
      $xpath = new Xpath($source->ownerDocument);
      return $xpath('*', $source);
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    private function getType(\DOMElement $node): string {
      if ($node->hasAttributeNS(self::XMLNS_JSONDOM, 'type')) {
        return $node->getAttributeNS(self::XMLNS_JSONDOM, 'type');
      }
      $xpath = new Xpath($node->ownerDocument);
      return $xpath('count(*) > 0', $node) ? 'object' : 'string';
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    private function getKey(\DOMElement $node): string {
      if ($node->hasAttributeNS(self::XMLNS_JSONDOM, 'name')) {
        return $node->getAttributeNS(self::XMLNS_JSONDOM, 'name');
      }
      return $node->localName;
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
    protected function getNamespaces(\DOMElement $node): array {
      $result = $this->getAllNamespaces($node);
      $inherited = [];
      if ($node->parentNode instanceOf \DOMElement) {
        $inherited = $this->getAllNamespaces($node->parentNode);
      }
      return array_diff_assoc($result, $inherited);
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    private function getAllNamespaces(\DOMElement $node): array {
      $xpath = new Xpath($node->ownerDocument);
      $result = [];
      /** @var \DOMNodeList $nodes */
      $nodes = $xpath->evaluate('namespace::*', $node);
      foreach ($nodes as $namespaceNode) {
        if (
          ($namespaceNode->nodeName !== 'xmlns:xml') &&
          ($namespaceNode->nodeName !== 'xmlns:xmlns')
        ) {
          $result[$namespaceNode->nodeName] = $namespaceNode->namespaceURI;
        }
      }
      return $result;
    }
  }
}
