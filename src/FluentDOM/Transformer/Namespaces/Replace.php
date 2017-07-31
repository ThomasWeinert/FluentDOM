<?php
/**
 * Allows to replace namespaces.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Transformer\Namespaces {

  use FluentDOM\Transformer\Namespaces;

  /**
   * Replace namespaces in a document, prefixes are copied, but might be optimized by
   * libxml.
   *
   * Attributes and Elements without a namespace get their prefix removed.
   * A change from no namespace to a namespace will not affect attributes.
   */
  class Replace extends Namespaces {

    /**
     * @var array
     */
    private $_namespaces;

    /**
     * @var array
     */
    private $_prefixes;

    public function __construct(\DOMNode $node, array $namespaces, array $prefixes = []) {
      parent::__construct($node);
      $this->_namespaces = $namespaces;
      $this->_prefixes = $prefixes;
    }

    /**
     * @param \DOMNode $parent
     * @param \DOMNode $source
     */
    protected function addNode(\DOMNode $parent, \DOMNode $source) {
      if ($source instanceof \DOMElement) {
        $this->importElement($parent, $source);
      } else {
        $document = $parent instanceof \DOMDocument ? $parent : $parent->ownerDocument;
        $parent->appendChild($document->importNode($source));
      }
    }

    /**
     * @param \DOMNode $parent
     * @param \DOMElement $source
     */
    private function importElement(\DOMNode $parent, \DOMElement $source) {
      $document = $parent instanceof \DOMDocument ? $parent : $parent->ownerDocument;
      $namespaceURI = $this->getMappedNamespace((string)$source->namespaceURI);
      $prefix = $this->getMappedPrefix($namespaceURI);
      if (empty($namespaceURI)) {
        $child = $document->createElement($source->localName);
      } elseif ($prefix === NULL) {
        $child = $document->createElementNS($namespaceURI, $source->nodeName);
      } else {
        $child = $document->createElementNS($namespaceURI, $prefix.':'.$source->localName);
      }
      $parent->appendChild($child);
      foreach ($source->attributes as $attribute) {
        $this->importAttribute($child, $attribute);
      }
      foreach ($source->childNodes as $childNode) {
        $this->addNode($child, $childNode);
      }
    }

    /**
     * @param \DOMElement $parent
     * @param \DOMAttr $source
     */
    private function importAttribute(\DOMElement $parent, \DOMAttr $source) {
      $document = $parent instanceof \DOMDocument ? $parent : $parent->ownerDocument;
      $namespaceURI = $this->getMappedNamespace((string)$source->namespaceURI);
      $prefix = $this->getMappedPrefix($namespaceURI) ?? $source->prefix;
      if (empty($namespaceURI) || empty($prefix)) {
        $attribute = $document->createAttribute($source->localName);
      } else {
        $attribute = $document->createAttributeNS($namespaceURI, $prefix.':'.$source->localName);
      }
      $attribute->value = $source->value;
      $parent->setAttributeNode($attribute);
    }

    /**
     * @param string $namespaceURI
     * @return string
     */
    private function getMappedNamespace(string $namespaceURI): string {
      if (isset($this->_namespaces[$namespaceURI])) {
        return (string)$this->_namespaces[$namespaceURI];
      }
      return $namespaceURI;
    }

    /**
     * @param string $namespaceURI
     * @return string|NULL
     */
    private function getMappedPrefix(string $namespaceURI) {
      if (isset($this->_prefixes[$namespaceURI])) {
        return (string)$this->_prefixes[$namespaceURI];
      }
      return NULL;
    }
  }
}
