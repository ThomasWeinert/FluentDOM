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

namespace FluentDOM\Query {

  /**
   * FluentDOM\Data is used for the FluentDOM::data property and FluentDOM::data() method, providing an
   * interface html5 data properties of a node.
   */
  class Data implements \IteratorAggregate, \Countable {

    /**
     * Attached element node
     *
     * @var \DOMElement
     */
    private $_node;

    /**
     * Create object with attached element node.
     *
     * @param \DOMElement $node
     */
    public function __construct(\DOMElement $node) {
      $this->_node = $node;
    }

    public function getOwner(): \DOMElement {
      return $this->_node;
    }

    /**
     * Convert data attributes into an array
     *
     * @return array
     */
    public function toArray(): array {
      $result = [];
      foreach ($this->_node->attributes as $attribute) {
        if ($this->isDataProperty($attribute->name)) {
          $result[$this->decodeName($attribute->name)] = $this->decodeValue($attribute->value);
        }
      }
      return $result;
    }

    /**
     * IteratorAggregate Interface: allow to iterate the data attributes
     *
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      return new \ArrayIterator($this->toArray());
    }

    /**
     * countable Interface: return the number of data attributes
     *
     * @return int
     */
    public function count(): int {
      $result = 0;
      foreach ($this->_node->attributes as $attribute) {
        if ($this->isDataProperty($attribute->name)) {
          ++$result;
        }
      }
      return $result;
    }

    /**
     * Validate if the attached node has the data attribute.
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool {
      return $this->_node->hasAttribute($this->encodeName($name));
    }

    /**
     * Change a data attribute on the attached node.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) {
      $this->_node->setAttribute($this->encodeName($name), $this->encodeValue($value));
    }

    /**
     * Read a data attribute from the attached node.
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
      $name = $this->encodeName($name);
      if ($this->_node->hasAttribute($name)) {
        return $this->decodeValue($this->_node->getAttribute($name));
      }
      return NULL;
    }

    /**
     * Remove a data attribute from the attached node.
     *
     * @param string $name
     */
    public function __unset(string $name): void {
      $this->_node->removeAttribute($this->encodeName($name));
    }

    /**
     * Validate if the given attribute name is a data property name
     *
     * @param string $name
     * @return bool
     */
    private function isDataProperty(string $name): bool {
      return (0 === \strpos($name, 'data-') && $name === \strtolower($name));
    }

    /**
     * Normalize a property name from camel case to lowercase with hyphens.
     *
     * @param string $name
     * @return string
     */
    private function encodeName(string $name): string {
      if (\preg_match('(^[a-z][a-z\d]*([A-Z]+[a-z\d]*)+$)DS', $name)) {
        $camelCasePattern = '((?:[a-z][a-z\d]+)|(?:[A-Z][a-z\d]+)|(?:[A-Z]+(?![a-z\d])))S';
        if (\preg_match_all($camelCasePattern, $name, $matches)) {
          $name = \implode('-', $matches[0]);
        }
      }
      return 'data-'.\strtolower($name);
    }

    /**
     * Convert the given attribute name with hyphens to camel case.
     *
     * @param string $name
     * @return string
     */
    private function decodeName(string $name): string {
      $parts = \explode('-', \strtolower(\substr($name, 5)));
      $result = \array_shift($parts);
      foreach ($parts as $part) {
        $result .= \ucfirst($part);
      }
      return $result;
    }

    /**
     * Decode the attribute value into a php variable/array/object
     *
     * @param string $value
     * @return mixed
     */
    private function decodeValue(string $value) {
      switch (TRUE) {
      case ($value === 'true') :
        return TRUE;
      case ($value === 'false') :
        return FALSE;
      case ($this->isJsonString($value)) :
        if ($json = \json_decode($value, FALSE)) {
          return $json;
        }
        return NULL;
      default :
        return $value;
      }
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isJsonString(string $value): bool {
      $firstChar = $value[0] ?? '';
      return $firstChar === '{' ||$firstChar === '[';
    }

    /**
     * Encode php variable into a string. Array or Objects will be serialized using json encoding.
     * Boolean use the strings yes/no.
     *
     * @param mixed $value
     * @return string
     */
    private function encodeValue($value): string {
      if (\is_bool($value)) {
        return $value ? 'true' : 'false';
      }
      if (\is_object($value) || \is_array($value)) {
        return \json_encode($value);
      }
      return (string)$value;
    }
  }
}
