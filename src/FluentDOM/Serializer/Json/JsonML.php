<?php
/**
 * Serialize a DOM to JsonML: http://www.jsonml.org/
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Serializer\Json {

  use FluentDOM\Serializer\Json;

  /**
   * Serialize a DOM to JsonML: http://www.jsonml.org/
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
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
     * @return mixed
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
          $childNode instanceof \DOMCharacterData
        ) {
          $result[] = $this->getValue($childNode->data);
        }
      }
      return $result;
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    private function getAttributes(\DOMElement $node): array {
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
      }
      if ($this->isInteger($value)) {
        return (int)$value;
      }
      if ($this->isNumber($value)) {
        return (float)$value;
      }
      return $value;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isInteger($value): bool {
      return (bool)preg_match('(^[1-9]\d*$)D', $value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isNumber($value): bool {
      return (bool)preg_match('(^(?:\\d+\\.\\d+|[1-9]\d*)$)D', $value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isBoolean($value): bool {
      return (bool)preg_match('(^(?:true|false)$)Di', $value);
    }
  }
}