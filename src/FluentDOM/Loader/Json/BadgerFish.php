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
  class BadgerFish implements Loadable {

    use Supports\Json;

    /**
     * @return string[]
     */
    public function getSupported() {
      return ['badgerfish', 'application/badgerfish', 'application/badgerfish+json'];
    }

    /**
     * @param \DOMNode|\DOMElement $node
     * @param mixed $json
     */
    protected function transferTo(\DOMNode $node, $json) {
      if (is_object($json)) {
        /** @var Document $dom */
        $dom = $node->ownerDocument ?: $node;
        if (is_object($json)) {
          foreach ($json as $name => $data) {
            if ($name === '@xmlns') {
              $this->transferNamespacesTo($node, $data);
            } elseif ($name === '$') {
              // text content
              $node->appendChild(
                $dom->createTextNode($this->getValueAsString($data))
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
     * @param string|NULL $prefix
     * @return string
     */
    protected function getNamespacePropertyName($prefix) {
      return empty($prefix) ? '$' : $prefix;
    }

    /**
     * @param \DOMElement $node
     * @param \stdClass $data
     */
    protected function transferNamespacesTo(\DOMElement $node, $data) {
      foreach ($data as $key => $namespace) {
        $prefix = $key === '$' ? NULL : $key;
        if ($node->lookupNamespaceUri($prefix) != $namespace) {
          $node->setAttribute(
            empty($prefix) ? 'xmlns' : 'xmlns:' . $prefix,
            $namespace
          );
        }
      }
    }

    /**
     * @param \DOMElement $node
     * @param string $name
     * @param string|number|boolean|NULL $data
     * @return array
     */
    protected function transferAttributeTo(\DOMElement $node, $name, $data) {
      /** @var Document $dom */
      $dom = $node->ownerDocument ?: $node;
      $name = substr($name, 1);
      $namespace = $this->getNamespaceForNode($name, new \stdClass(), $node);
      $attribute = empty($namespace)
        ? $dom->createAttribute($name)
        : $dom->createAttributeNS($namespace, $name);
      $attribute->value = $this->getValueAsString($data);
      $node->setAttributeNode($attribute);
    }

    /**
     * @param \DOMNode $node
     * @param string $name
     * @param mixed $data
     * @return array
     */
    protected function transferChildTo(\DOMNode $node, $name, $data) {
      /** @var Document $dom */
      $dom = $node->ownerDocument ?: $node;
      $namespace = $this->getNamespaceForNode(
        $name,
        isset($data->{'@xmlns'}) ? $data->{'@xmlns'} : new \stdClass(),
        $dom
      );
      if (!is_array($data)) {
        $data = [$data];
      }
      foreach ($data as $dataChild) {
        $child = $node->appendChild(
          empty($namespace) ? $dom->createElement($name) : $dom->createElementNS($namespace, $name)
        );
        $this->transferTo($child, $dataChild);
      }
      return $data;
    }
  }
}