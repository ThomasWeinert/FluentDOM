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

namespace FluentDOM\Loader\Json {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\LoaderSupports;

  /**
   * Load a DOM document from a json string or file
   */
  class BadgerFishLoader implements Loadable {

    use LoaderSupports\JsonSupports;

    public const CONTENT_TYPES = [
      'badgerfish', 'application/badgerfish', 'application/badgerfish+json'
    ];

    /**
     * @throws \LogicException|\DOMException
     */
    protected function transferTo(\DOMNode $target, mixed $json): void {
      /** @var Document $document */
      $document = $target->ownerDocument ?: $target;
      if ($json instanceof \stdClass) {
        foreach ($json as $name => $data) {
          if ($name === '@xmlns' && $target instanceof Element) {
            $this->transferNamespacesTo($target, $data);
          } elseif ($name === '$') {
            // text content
            $target->appendChild(
              $document->createTextNode($this->getValueAsString($data))
            );
          } elseif (str_starts_with($name, '@') && $target instanceof Element) {
            $this->transferAttributeTo($target, $name, $data);
          } else {
            $this->transferChildTo($target, $name, $data);
          }
        }
      }
    }

    /**
     * Get the property name for a namespace prefix
     */
    protected function getNamespacePropertyName(string $prefix): string {
      return empty($prefix) ? '$' : $prefix;
    }

    /**
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
     * @throws \LogicException|\DOMException
     */
    protected function transferAttributeTo(
      Element $node,
      string $name,
      string|int|float|bool|NULL $data
    ): void {
      /** @var Document $document */
      $document = $node->ownerDocument ?: $node;
      $name = substr($name, 1);
      $namespaceURI = (string)$this->getNamespaceForNode($name, new \stdClass(), $node);
      $attribute = '' === $namespaceURI
        ? $document->createAttribute($name)
        : $document->createAttributeNS($namespaceURI, $name);
      $attribute->value = $this->getValueAsString($data);
      $node->setAttributeNode($attribute);
    }

    /**
     * @throws \LogicException|\DOMException
     */
    protected function transferChildTo(\DOMNode $node, string $name, mixed $data): array {
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
