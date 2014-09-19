<?php
/**
 * Allows to replace namespaces.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Transformer\Namespaces {

  /**
   * Replace namespaces in a document, prefixes are copied, but might be optimized by
   * libxml.
   *
   * Attributes and Elements without a namespace get their prefix removed.
   * A change from no namespace to a namespace will not affect attributes.
   */
  class Replace {

    /**
     * @var \DOMDocument
     */
    private $_document = null;

    /**
     * @var array
     */
    private $_namespaces = [];

    public function __construct(
      \DOMDocument $document, array $namespaces
    ) {
      $this->_document = $document;
      $this->_namespaces = $namespaces;
    }

    /**
     * Create a document with the replaced namespaces and return it
     * as XML string.
     *
     * @return string
     */
    public function __toString() {
      return $this->getDocument()->saveXml();
    }

    /**
     * Create a document with the replaced namespaces.
     */
    public function getDocument() {
      $result = new \DOMDocument();
      foreach ($this->_document->childNodes as $childNode) {
         $this->importNode($result, $childNode);
      }
      return $result;
    }

    /**
     * @param \DOMNode $parent
     * @param \DOMNode $source
     */
    private function importNode(\DOMNode $parent, \DOMNode $source) {
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
      $namespaceUri = $this->getMappedNamespace($source->namespaceURI);
      if (empty($namespaceUri)) {
        $child = $document->createElement($source->localName);
      } else {
        $child = $document->createElementNS($namespaceUri, $source->nodeName);
      }
      $parent->appendChild($child);
      foreach ($source->attributes as $attribute) {
        $this->importAttribute($child, $attribute);
      }
      foreach ($source->childNodes as $childNode) {
        $this->importNode($child, $childNode);
      }
    }

    /**
     * @param \DOMElement $parent
     * @param \DOMAttr $source
     */
    private function importAttribute(\DOMElement $parent, \DOMAttr $source) {
      $document = $parent instanceof \DOMDocument ? $parent : $parent->ownerDocument;
      $namespaceUri = $this->getMappedNamespace($source->namespaceURI);
      if (empty($namespaceUri) || empty($source->prefix)) {
        $attribute = $document->createAttribute($source->localName);
      } else {
        $attribute = $document->createAttributeNS($namespaceUri, $source->nodeName);
      }
      $attribute->value = $source->value;
      $parent->setAttributeNode($attribute);
    }

    /**
     * @param string $namespaceUri
     * @return string
     */
    private function getMappedNamespace($namespaceUri) {
      if (isset($this->_namespaces[$namespaceUri])) {
        return $this->_namespaces[$namespaceUri];
      }
      return $namespaceUri;
    }
  }
}
