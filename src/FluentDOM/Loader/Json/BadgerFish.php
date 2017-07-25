<?php
/**
 * Load a DOM document from a json string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;

  /**
   * Load a DOM document from a json string or file
   */
  class BadgerFish implements Loadable {

    use Supports\Json;

    /**
     * @return string[]
     */
    public function getSupported(): array {
      return ['badgerfish', 'application/badgerfish', 'application/badgerfish+json'];
    }

    /**
     * @param \DOMNode|\DOMElement $node
     * @param mixed $json
     */
    protected function transferTo(\DOMNode $node, $json) {
      if (is_object($json)) {
        /** @var Document $document */
        $document = $node->ownerDocument ?: $node;
        if (is_object($json)) {
          foreach ($json as $name => $data) {
            if ($name === '@xmlns') {
              $this->transferNamespacesTo($node, $data);
            } elseif ($name === '$') {
              // text content
              $node->appendChild(
                $document->createTextNode($this->getValueAsString($data))
              );
            } elseif (substr($name, 0, 1) === '@') {
              $this->transferAttributeTo($node, $name, $data);
            } else {
              $this->transferChildTo($node, $name, $data);
            }
          }
        }
      }
    }

    /**
     * Get the property name for a namespace prefix
     *
     * @param string $prefix
     * @return string
     */
    protected function getNamespacePropertyName(string $prefix): string {
      return empty($prefix) ? '$' : $prefix;
    }

    /**
     * @param \DOMElement $node
     * @param \stdClass $data
     */
    protected function transferNamespacesTo(\DOMElement $node, $data) {
      foreach ($data as $key => $namespaceURI) {
        $prefix = $key === '$' ? NULL : $key;
        if ($node->lookupNamespaceUri($prefix) != $namespaceURI) {
          $node->setAttribute(
            empty($prefix) ? 'xmlns' : 'xmlns:' . $prefix,
            $namespaceURI
          );
        }
      }
    }

    /**
     * @param \DOMElement $node
     * @param string $name
     * @param string|number|bool|NULL $data
     * @return array
     */
    protected function transferAttributeTo(\DOMElement $node, string $name, $data) {
      /** @var Document $document */
      $document = $node->ownerDocument ?: $node;
      $name = substr($name, 1);
      $namespaceURI = $this->getNamespaceForNode($name, new \stdClass(), $node);
      $attribute = empty($namespaceURI)
        ? $document->createAttribute($name)
        : $document->createAttributeNS($namespaceURI, $name);
      $attribute->value = $this->getValueAsString($data);
      $node->setAttributeNode($attribute);
    }

    /**
     * @param \DOMNode $node
     * @param string $name
     * @param mixed $data
     * @return array
     */
    protected function transferChildTo(\DOMNode $node, string $name, $data) {
      /** @var Document $document */
      $document = $node->ownerDocument ?: $node;
      $namespaceURI = $this->getNamespaceForNode(
        $name,
        isset($data->{'@xmlns'}) ? $data->{'@xmlns'} : new \stdClass(),
        $document
      );
      if (!is_array($data)) {
        $data = [$data];
      }
      foreach ($data as $dataChild) {
        $child = $node->appendChild(
          empty($namespaceURI)
            ? $document->createElement($name) : $document->createElementNS($namespaceURI, $name)
        );
        $this->transferTo($child, $dataChild);
      }
      return $data;
    }
  }
}