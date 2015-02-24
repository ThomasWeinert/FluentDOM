<?php
/**
 * Load a DOM document from a json string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\Document;
  use FluentDOM\Element;
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
    public function getSupported() {
      return ['rayfish', 'application/rayfish', 'application/rayfish+json'];
    }

    /**
     * @param \DOMNode|\DOMElement $node
     * @param mixed $json
     */
    protected function transferTo(\DOMNode $node, $json) {
      if (is_object($json)) {
        /** @var Document $dom */
        $dom = $node->ownerDocument ?: $node;
        $nodeName = $json->{'#name'};
        list($attributes, $namespaces) = $this->getAttributes($json);
        $child = $dom->createElementNS(
          $this->getNamespaceForNode($nodeName, $namespaces, $node),
          $nodeName
        );
        $node->appendChild($child);
        $this->transferText($dom, $child, $json);
        $this->transferChildren($child, $json, $namespaces, $attributes);
      }
    }

    /**
     * @param \DOMDocument $dom
     * @param \DOMElement $target
     * @param \stdClass $json
     */
    private function transferText(\DOMDocument $dom, \DOMElement $target, $json) {
      if (isset($json->{'#text'})) {
        $target->appendChild($dom->createTextNode($json->{'#text'}));
      }
    }

    /**
     * @param \DOMElement $target
     * @param \stdClass $json
     * @param \stdClass $namespaces
     * @param \stdClass $attributes
     */
    private function transferChildren(
      \DOMElement $target, $json, $namespaces, $attributes
    ) {
      if (isset($json->{'#children'})) {
        $this->transferAttributes($target, $namespaces, $attributes);
        foreach ($json->{'#children'} as $value) {
          $name = isset($value->{'#name'}) ? $value->{'#name'} : '@';
          if (substr($name, 0, 1) != '@') {
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
    private function transferAttributes(Element $node, $namespaces, $attributes) {
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
    private function getAttributes($json) {
      $attributes = new \stdClass();
      $namespaces = new \stdClass();
      if (isset($json->{'#children'})) {
        foreach ($json->{'#children'} as $child) {
          $name = isset($child->{'#name'}) ? $child->{'#name'} : '';
          $value = isset($child->{'#text'}) ? $child->{'#text'} : '';
          if ($name === '@xmlns' || substr($name, 0, 7) === '@xmlns:') {
            $namespaces->{substr($name, 1)} = $value;
          } elseif (substr($name, 0, 1) === '@') {
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