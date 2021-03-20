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

namespace FluentDOM\Loader\Json {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;

  /**
   * Load a DOM document from a json string or file
   */
  class BadgerFish implements Loadable {

    use Supports\Json;
    public const CONTENT_TYPES = ['badgerfish', 'application/badgerfish', 'application/badgerfish+json'];

    /**
     * @param \DOMNode|Element $target
     * @param mixed $json
     * @throws \LogicException
     */
    protected function transferTo(\DOMNode $target, $json): void {
      /** @var Document $document */
      $document = $target->ownerDocument ?: $target;
      if ($json instanceof \stdClass) {
        foreach ($json as $name => $data) {
          if ($name === '@xmlns') {
            $this->transferNamespacesTo($target, $data);
          } elseif ($name === '$') {
            // text content
            $target->appendChild(
              $document->createTextNode($this->getValueAsString($data))
            );
          } elseif (0 === strpos($name, '@')) {
            $this->transferAttributeTo($target, $name, $data);
          } else {
            $this->transferChildTo($target, $name, $data);
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
     * @param Element $node
     * @param \stdClass $data
     * @throws \LogicException
     */
    protected function transferNamespacesTo(Element $node, \stdClass $data): void {
      foreach ($data as $key => $namespaceURI) {
        $prefix = $key === '$' ? NULL : $key;
        if ((string)$node->lookupNamespaceUri($prefix) !== $namespaceURI) {
          $node->setAttribute(
            empty($prefix) ? 'xmlns' : 'xmlns:' . $prefix,
            $namespaceURI
          );
        }
      }
    }

    /**
     * @param Element $node
     * @param string $name
     * @param string|number|bool|NULL $data
     * @throws \LogicException
     */
    protected function transferAttributeTo(Element $node, string $name, $data): void {
      /** @var Document $document */
      $document = $node->ownerDocument ?: $node;
      $name = (string)\substr($name, 1);
      $namespaceURI = (string)$this->getNamespaceForNode($name, new \stdClass(), $node);
      $attribute = '' === $namespaceURI
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
     * @throws \LogicException
     */
    protected function transferChildTo(\DOMNode $node, string $name, $data): array {
      /** @var Document $document */
      $document = $node->ownerDocument ?: $node;
      $namespaceURI = (string)$this->getNamespaceForNode(
        $name,
        $data->{'@xmlns'} ?? new \stdClass(),
        $document
      );
      if (!\is_array($data)) {
        $data = [$data];
      }
      foreach ($data as $dataChild) {
        $child = $node->appendChild(
          '' === $namespaceURI
            ? $document->createElement($name) : $document->createElementNS($namespaceURI, $name)
        );
        $this->transferTo($child, $dataChild);
      }
      return $data;
    }
  }
}
