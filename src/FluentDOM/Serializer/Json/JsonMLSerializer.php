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

namespace FluentDOM\Serializer\Json {

  use FluentDOM\Serializer\JsonSerializer;

  /**
   * Serialize a DOM to JsonMLLoader: http://www.jsonml.org/
   */
  class JsonMLSerializer extends JsonSerializer {

    /**
     * @return array
     */
    protected function getEmpty(): array {
      return [];
    }

    /**
     * @param \DOMElement $node
     * @return mixed
     */
    protected function getNode(\DOMElement $node): array {
      $result = [
        $node->nodeName
      ];
      $attributes = \array_merge(
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

    private function getAttributes(\DOMElement $node): array {
      $result = [];
      foreach ($node->attributes as $name => $attribute) {
        $result[$name] = $this->getValue($attribute->value);
      }
      return $result;
    }

    /**
     * Get value prepared for JsonLoader data structure
     */
    private function getValue(mixed $value): mixed {
      if ($this->isBoolean($value)) {
        return (\strtolower($value) === 'true');
      }
      if ($this->isInteger($value)) {
        return (int)$value;
      }
      if ($this->isNumber($value)) {
        return (float)$value;
      }
      return $value;
    }

    private function isInteger(mixed $value): bool {
      return (bool)\preg_match('(^[1-9]\d*$)D', $value);
    }

    private function isNumber(mixed $value): bool {
      return (bool)\preg_match('(^(?:\\d+\\.\\d+|[1-9]\d*)$)D', $value);
    }

    private function isBoolean(mixed $value): bool {
      return (bool)\preg_match('(^(?:true|false)$)Di', $value);
    }
  }
}
