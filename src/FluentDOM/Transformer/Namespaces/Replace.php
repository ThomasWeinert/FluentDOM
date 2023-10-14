<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Transformer\Namespaces {

  use FluentDOM\DOM\Implementation;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Transformer\Namespaces;

  /**
   * Replace namespaces in a document, prefixes are copied, but might be optimized by
   * libxml.
   *
   * Attributes and Elements without a namespace get their prefix removed.
   * A change from no namespace to a namespace will not affect attributes.
   */
  class Replace extends Namespaces {

    private array $_namespaces;

    private array $_prefixes;

    public function __construct(\DOMNode $node, array $namespaces, array $prefixes = []) {
      parent::__construct($node);
      $this->_namespaces = $namespaces;
      $this->_prefixes = $prefixes;
    }

    /**
     * @throws UnattachedNode|\DOMException
     */
    protected function addNode(\DOMNode $target, \DOMNode $source): void {
      if ($source instanceof \DOMElement) {
        $this->importElement($target, $source);
      } else {
        $document = Implementation::getNodeDocument($target);
        $target->appendChild($document->importNode($source));
      }
    }

    /**
     * @throws UnattachedNode|\DOMException
     */
    private function importElement(\DOMNode $target, \DOMElement $source): void {
      $document = Implementation::getNodeDocument($target);
      $namespaceURI = $this->getMappedNamespace((string)$source->namespaceURI);
      $prefix = $this->getMappedPrefix($namespaceURI);
      if (empty($namespaceURI)) {
        $child = $document->createElement($source->localName);
      } elseif ($prefix === NULL) {
        $child = $document->createElementNS($namespaceURI, $source->nodeName);
      } else {
        $child = $document->createElementNS($namespaceURI, $prefix.':'.$source->localName);
      }
      $target->appendChild($child);
      foreach ($source->attributes as $attribute) {
        $this->importAttribute($child, $attribute);
      }
      foreach ($source->childNodes as $childNode) {
        $this->addNode($child, $childNode);
      }
    }

    private function importAttribute(\DOMElement $parent, \DOMAttr $source): void {
      $document = $parent->ownerDocument;
      $namespaceURI = $this->getMappedNamespace((string)$source->namespaceURI);
      $prefix = $this->getMappedPrefix($namespaceURI) ?? $source->prefix;
      if (empty($namespaceURI) || NULL === $prefix || '' === $prefix) {
        $attribute = $document->createAttribute($source->localName);
      } else {
        $attribute = $document->createAttributeNS($namespaceURI, $prefix.':'.$source->localName);
      }
      $attribute->value = $source->value;
      $parent->setAttributeNode($attribute);
    }

    private function getMappedNamespace(string $namespaceURI): string {
      if (isset($this->_namespaces[$namespaceURI])) {
        return (string)$this->_namespaces[$namespaceURI];
      }
      return $namespaceURI;
    }

    private function getMappedPrefix(string $namespaceURI): ?string {
      if (isset($this->_prefixes[$namespaceURI])) {
        return (string)$this->_prefixes[$namespaceURI];
      }
      return NULL;
    }
  }
}
