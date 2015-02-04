<?php
/**
 * Load a DOM document from a json string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;

  /**
   * Load a DOM document from a json string or file
   */
  class JsonML implements Loadable {

    use Supports\Json;

    /**
     * @return string[]
     */
    public function getSupported() {
      return ['jsonml', 'application/jsonml', 'application/jsonml+json'];
    }

    /**
     * @param \DOMNode|\DOMElement $node
     * @param mixed $json
     */
    public function transferTo(\DOMNode $node, $json) {
      if (is_array($json) && count($json) > 0) {
        $this->transferToElement($node, $json);
      } elseif (is_scalar($json)) {
        $dom = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
        $node->appendChild(
          $dom->createTextNode($this->getValueAsString($json))
        );
      }
    }

    /**
     * @param \DOMElement $node
     * @param \stdClass $properties
     */
    private function addNamespaceAttributes(\DOMElement $node, $properties) {
      foreach ($properties as $name => $value) {
        if ($name === 'xmlns' || substr($name, 0, 6) === 'xmlns:') {
          if ($node instanceof \DOMElement) {
            $prefix = $name === 'xmlns' ? NULL : substr($name, 6);
            if ($node->lookupNamespaceUri($prefix) != $value) {
              $node->setAttribute($name, $value);
            }
          }
        }
      }
    }

    /**
     * @param \DOMElement $node
     * @param \stdClass $properties
     */
    private function addAttributes(\DOMElement $node, $properties) {
      $dom = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
      foreach ($properties as $name => $value) {
        if (!($name === 'xmlns' || substr($name, 0, 6) === 'xmlns:')) {
          $namespace = $this->getNamespaceForNode($name, $properties, $node);
          $attribute = empty($namespace)
            ? $dom->createAttribute($name)
            : $dom->createAttributeNS($namespace, $name);
          $attribute->value = $this->getValueAsString($value);
          $node->setAttributeNode($attribute);
        }
      }
    }

    /**
     * @param \DOMNode $node
     * @param $json
     */
    private function transferToElement(\DOMNode $node, $json) {
      $dom = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
      $nodeName = $json[0];
      $length = count($json);
      $hasProperties = $length > 1 && is_object($json[1]);
      $properties = $hasProperties ? $json[1] : new \stdClass;
      $namespace = $this->getNamespaceForNode($nodeName, $properties, $node);
      $element = empty($namespace)
        ? $dom->createElement($nodeName)
        : $dom->createElementNS($namespace, $nodeName);
      $node->appendChild($element);
      $this->addNamespaceAttributes($element, $properties);
      $this->addAttributes($element, $properties);
      $childOffset = $hasProperties ? 2 : 1;
      for ($i = $childOffset; $i < $length; $i++) {
        $this->transferTo($element, $json[$i]);
      }
    }
  }
}