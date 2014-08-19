<?php
/**
 * Serialize a DOM to JsonML: http://www.jsonml.org/
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer\Json {

  use FluentDOM\Serializer\Json;

  /**
   * Serialize a DOM to JsonML: http://www.jsonml.org/
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  class JsonML extends Json {

    /**
     * @return array
     */
    protected function getEmpty() {
      return [];
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    protected function getNode(\DOMElement $node) {
      $result = [
        $node->nodeName
      ];
      $attributes = array_merge(
        $this->getNamespaces($node),
        $this->getAttributes($node)
      );
      if (!empty($attributes)) {
        $result[] = $attributes;
      }
      foreach ($node->childNodes as $childNode) {
        if ($childNode instanceof \DOMElement) {
          $result[] = $this->getNode($childNode);
        } elseif (
          $childNode instanceof \DOMText ||
          $childNode instanceof \DOMCdataSection
        ) {
          $result[] = $this->getValue($childNode->data);
        }
      }
      return $result;
    }

    /**
     * @param \DOMElement $node
     * @return array|NULL
     */
    private function getAttributes(\DOMElement $node) {
      $result = [];
      foreach ($node->attributes as $name => $attribute) {
        $result[$name] = $this->getValue($attribute->value);
      }
      return $result;
    }

    /**
     * Get value prepared for Json data structure
     *
     * @param mixed $value
     * @return mixed
     */
    private function getValue($value) {
      if ($this->isBoolean($value)) {
        return (strtolower($value) === 'true');
      } elseif ($this->isInteger($value)) {
        return (int)$value;
      } elseif ($this->isNumber($value)) {
        return (float)$value;
      }
      return $value;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isInteger($value) {
      return (bool)preg_match('(^[1-9]\d*$)D', $value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isNumber($value) {
      return (bool)preg_match('(^(?:\\d+\\.\\d+|[1-9]\d*)$)D', $value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isBoolean($value) {
      return (bool)preg_match('(^(?:true|false)$)Di', $value);
    }
  }
}