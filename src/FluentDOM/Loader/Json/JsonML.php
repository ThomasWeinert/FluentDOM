<?php
/**
 * Load a DOM document from a json string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\Document;
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
     * Load the json string into an DOMDocument
     *
     * @param mixed $source
     * @param string $contentType
     * @throws \UnexpectedValueException
     * @return Document|NULL
     */
    public function load($source, $contentType) {
      $json = $source;
      if (is_string($source)) {
        $json = $this->getJson($source);
      }
      if ($json || is_array($json)) {
        $dom = new Document('1.0', 'UTF-8');
        $this->transferTo($dom, $json);
        return $dom;
      }
      return NULL;
    }

    /**
     * @param \DOMNode|\DOMElement $node
     * @param mixed $json
     */
    public function transferTo(\DOMNode $node, $json) {
      /** @var Document $dom */
      $dom = $node->ownerDocument ?: $node;
      $length = count($json);
      if (is_array($json) && $length > 0) {
        $nodeName = $json[0];
        $hasProperties = $length > 1 && is_object($json[1]);
        $properties = $hasProperties ? $json[1] : new \stdClass;
        $namespace = $this->getNamespace($nodeName, $properties, $node);
        $node->appendChild(
          $element = empty($namespace)
            ? $dom->createElement($nodeName)
            : $dom->createElementNS($namespace, $nodeName)
        );
        $this->addNamespaceAttributes($element, $properties);
        $this->addAttributes($element, $properties);
        $childOffset = $hasProperties ? 2 : 1;
        for ($i = $childOffset; $i < $length; $i++) {
          $this->transferTo($element, $json[$i]);
        }
      } elseif (is_scalar($json)) {
        $node->appendChild(
          $dom->createTextNode($this->getValueAsString($json))
        );
      }
    }

    /**
     * @param string $nodeName
     * @param \stdClass $properties
     * @param \DOMNode $node
     * @return string
     */
    private function getNamespace(
      $nodeName, \stdClass $properties, \DOMNode $node
    ) {
      if (strpos($nodeName, ':') >= 0) {
        $prefix = substr($nodeName, 0, strpos($nodeName, ':'));
      } else {
        $prefix = '';
      }
      $xmlns = empty($prefix) ? 'xmlns' : 'xmlns:'.$prefix;
      return isset($properties->{$xmlns})
        ? $properties->{$xmlns}
        : $node->lookupNamespaceUri(empty($prefix) ? NULL : $prefix);
    }

    /**
     * @param \DOMElement $node
     * @param \stdClass $properties
     */
    private function addNamespaceAttributes(\DOMElement $node, $properties) {
      foreach ($properties as $name => $value) {
        if ($name == 'xmlns' || substr($name, 0, 6) == 'xmlns:') {
          if ($node instanceof \DOMElement) {
            $prefix = $name == 'xmlns' ? NULL : substr($name, 6);
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
      /** @var Document $dom */
      $dom = $node->ownerDocument ?: $node;
      foreach ($properties as $name => $value) {
        if (!($name == 'xmlns' || substr($name, 0, 6) == 'xmlns:')) {
          $namespace = $this->getNamespace($name, $properties, $node);
          $attribute = empty($namespace)
            ? $dom->createAttribute($name)
            : $dom->createAttributeNS($namespace, $name);
          $attribute->value = $this->getValueAsString($value);
          $node->setAttributeNode($attribute);
        }
      }
    }
  }
}