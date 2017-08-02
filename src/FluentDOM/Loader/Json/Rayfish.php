<?php
/**
 * Load a DOM document from a json string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

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

    /**
     * @return string[]
     */
    public function getSupported(): array {
      return ['rayfish', 'application/rayfish', 'application/rayfish+json'];
    }

    /**
     * @param \DOMNode|Element $node
     * @param mixed $json
     */
    protected function transferTo(\DOMNode $node, $json) {
      if (is_object($json)) {
        /** @var Document $document */
        $document = $node->ownerDocument ?: $node;
        $nodeName = $json->{'#name'};
        list($attributes, $namespaces) = $this->getAttributes($json);
        $child = $document->createElementNS(
          $this->getNamespaceForNode($nodeName, $namespaces, $node),
          $nodeName
        );
        $node->appendChild($child);
        $this->transferText($document, $child, $json);
        $this->transferChildren($child, $json, $namespaces, $attributes);
      }
    }

    /**
     * @param Document $document
     * @param Element $target
     * @param \stdClass $json
     */
    private function transferText(Document $document, Element $target, $json) {
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
    ) {
      if (isset($json->{'#children'})) {
        $this->transferAttributes($target, $namespaces, $attributes);
        foreach ($json->{'#children'} as $value) {
          $name = $value->{'#name'} ?? '@';
          if (0 !== strpos($name, '@')) {
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
    private function transferAttributes(Element $node, \stdClass $namespaces, \stdClass $attributes) {
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
    private function getAttributes(\stdClass $json) {
      $attributes = new \stdClass();
      $namespaces = new \stdClass();
      if (isset($json->{'#children'})) {
        foreach ($json->{'#children'} as $child) {
          $name = $child->{'#name'} ?? '';
          $value = $child->{'#text'} ?? '';
          if ($name === '@xmlns' || 0 === strpos($name, '@xmlns:')) {
            $namespaces->{substr($name, 1)} = $value;
          } elseif (0 === strpos($name, '@')) {
            $attributes->{substr($name, 1)} = $value;
          }
        }
      }
      return [
        $attributes, $namespaces
      ];
    }
  }
}