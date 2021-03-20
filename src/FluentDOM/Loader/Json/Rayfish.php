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
  class Rayfish implements Loadable {

    use Supports\Json;
    public const CONTENT_TYPES = ['rayfish', 'application/rayfish', 'application/rayfish+json'];

    /**
     * @param \DOMNode|Element $target
     * @param mixed $json
     */
    protected function transferTo(\DOMNode $target, $json): void {
      if (\is_object($json)) {
        /** @var Document $document */
        $document = $target->ownerDocument ?: $target;
        $nodeName = $json->{'#name'};
        [$attributes, $namespaces] = $this->getAttributes($json);
        $child = $document->createElementNS(
          $this->getNamespaceForNode($nodeName, $namespaces, $target),
          $nodeName
        );
        $target->appendChild($child);
        $this->transferText($document, $child, $json);
        $this->transferChildren($child, $json, $namespaces, $attributes);
      }
    }

    /**
     * @param Document $document
     * @param Element $target
     * @param \stdClass $json
     */
    private function transferText(Document $document, Element $target, \stdClass $json): void {
      if (isset($json->{'#text'})) {
        $target->appendChild($document->createTextNode($json->{'#text'}));
      }
    }

    /**
     * @param Element $target
     * @param \stdClass $json
     * @param \stdClass $namespaces
     * @param \stdClass $attributes
     */
    private function transferChildren(
      Element $target, \stdClass $json, \stdClass $namespaces, \stdClass $attributes
    ): void {
      if (isset($json->{'#children'})) {
        $this->transferAttributes($target, $namespaces, $attributes);
        foreach ($json->{'#children'} as $value) {
          $name = $value->{'#name'} ?? '@';
          if (0 !== \strpos($name, '@')) {
            $this->transferTo($target, $value);
          }
        }
      }
    }

    /**
     * Transfer attributes to the node.
     *
     * @param Element $node
     * @param \stdClass $namespaces
     * @param \stdClass $attributes
     */
    private function transferAttributes(Element $node, \stdClass $namespaces, \stdClass $attributes): void {
      foreach ($namespaces as $name => $value) {
        $node->setAttribute($name, $value);
      }
      foreach ($attributes as $name => $value) {
        $node->setAttribute($name, $value);
      }
    }

    /**
     * @param \stdClass $json
     * @return \stdClass[]
     */
    private function getAttributes(\stdClass $json): array {
      $attributes = new \stdClass();
      $namespaces = new \stdClass();
      if (isset($json->{'#children'})) {
        foreach ($json->{'#children'} as $child) {
          $name = $child->{'#name'} ?? '';
          $value = $child->{'#text'} ?? '';
          if ($name === '@xmlns' || 0 === \strpos($name, '@xmlns:')) {
            $namespaces->{\substr($name, 1)} = $value;
          } elseif (0 === \strpos($name, '@')) {
            $attributes->{\substr($name, 1)} = $value;
          }
        }
      }
      return [
        $attributes, $namespaces
      ];
    }
  }
}
