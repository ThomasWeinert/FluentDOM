<?php
/**
 * Abstract superclass for json serializers (serialize a DOM into a Json structure).
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer {
  use FluentDOM\Xpath;

  /**
   * Abstract superclass for json serializers (serialize a DOM into a Json structure).
   *
   * See http://wiki.open311.org/index.php?title=JSON_and_XML_Conversion for
   * different formats.
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  abstract class Json implements \JsonSerializable {

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
     * @param \DOMDocument $document
     * @param int $options
     * @param int $depth
     */
    public function __construct(\DOMDocument $document, $options = 0, $depth = 512) {
      $this->_document = $document;
      $this->_options = (int)$options;
      $this->_depth = (int)$depth;
    }

    /**
     * @return string
     */
    public function __toString() {
      $json = version_compare(PHP_VERSION, '5.5.0', '>=')
        ? json_encode($this, $this->_options, $this->_depth)
        : json_encode($this, $this->_options);
      return ($json) ? $json : '';
    }

    /**
     * @return array|NULL
     */
    public function jsonSerialize() {
      if (isset($this->_document->documentElement)) {
        return $this->getNode($this->_document->documentElement);
      }
      return $this->getEmpty();
    }

    /**
     * @param \DOMElement $node
     * @return array|\stdClass|NULL
     */
    abstract protected function getNode(\DOMElement $node);

    /**
     * @return \stdClass|array
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