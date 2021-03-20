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

namespace FluentDOM\Query\Css {

  /**
   * Provides an array access to a css style string. It is used to
   * modify the attribute values of style attributes.
   */
  class Properties implements \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * Pattern to decode the style property string
     */
    private const STYLE_PATTERN = /** @lang TEXT */
      '((?:^|;)\s*(?P<name>[-\w]+)\s*:\s*(?P<value>[^;]+))';

    /**
     * property storage
     *
     * @var array
     */
    private $_properties = [];

    public function __construct(string $styleString = '') {
      $this->setStyleString($styleString);
    }

    public function __toString(): string {
      return $this->getStyleString();
    }

    /**
     * Decode style attribute to the css properties array.
     *
     * @param string $styleString
     */
    public function setStyleString(string $styleString): void {
      $this->_properties = [];
      if (!empty($styleString)) {
        $matches = [];
        if (\preg_match_all(self::STYLE_PATTERN, $styleString, $matches, PREG_SET_ORDER)) {
          foreach ($matches as $match) {
            if (
              isset($match['name'], $match['value']) &&
              $this->_isCSSProperty($match['name']) &&
              \trim($match['value']) !== '') {
              $this->_properties[$match['name']] = $match['value'];
            }
          }
        }
      }
    }

    /**
     * Encode css properties array for the style string.
     *
     * @return string
     */
    public function getStyleString(): string {
      $result = '';
      if (\is_array($this->_properties) && \count($this->_properties) > 0) {
        \uksort($this->_properties, new PropertyCompare());
        foreach ($this->_properties as $name => $value) {
          if (\trim($value) !== '') {
            $result .= ' '.$name.': '.$value.';';
          }
        }
      }
      return (string)\substr($result, 1);
    }

    /**
     * Get an iterator for the properties
     *
     * @see IteratorAggregate::getIterator()
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      return new \ArrayIterator($this->_properties);
    }

    /**
     * Get the property count of the first selected node
     *
     * @see Countable::count()
     * @return int
     */
    public function count(): int {
      return \count($this->_properties);
    }

    /**
     * Allow to use isset() and array syntax to check if a css property is set on
     * the first matched node.
     *
     * @param mixed $offset
     * @return bool
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset): bool {
      return isset($this->_properties[$offset]);
    }

    /**
     * Allow to use array syntax to read a css property value from first matched node.
     *
     * @see ArrayAccess::offsetGet()
     * @param mixed $offset
     * @return string $value
     */
    public function offsetGet($offset): string {
      return $this->_properties[$offset];
    }

    /**
     * Set a property
     *
     * @see ArrayAccess::offsetSet()
     * @param mixed $offset
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value): void {
      if ($this->_isCSSProperty($offset)) {
        if (\trim($value) !== '') {
          $this->_properties[$offset] = (string)$value;
        } else {
          $this->offsetUnset($offset);
        }
      } else {
        throw new \InvalidArgumentException('Invalid css property name: '.$offset);
      }
    }

    /**
     * Remove a css properties if it is set.
     *
     * @see ArrayAccess::offsetUnset()
     * @param string|string[] $offset
     */
    public function offsetUnset($offset): void {
      if (!\is_array($offset)) {
        $offset = [$offset];
      }
      foreach ($offset as $property) {
        if (\array_key_exists($property, $this->_properties)) {
          unset($this->_properties[(string)$property]);
        }
      }
    }

    /**
     * Compile value argument into a string (it can be an callback)
     *
     * @param string|callable $value
     * @param \DOMElement $node
     * @param int $index
     * @param string|NULL $currentValue
     * @return string
     */
    public function compileValue(
      $value, \DOMElement $node, int $index, string $currentValue = NULL
    ): string {
      if (!\is_string($value) && \is_callable($value, TRUE)) {
        return (string)$value($node, $index, $currentValue);
      }
      return (string)$value;
    }

    /**
     * Check if string is an valid css property name.
     *
     * @param string $propertyName
     * @return bool
     */
    private function _isCSSProperty(string $propertyName): bool {
      $pattern = '(^-?(?:[a-z]+-)*(?:[a-z]+)$)D';
      if (preg_match($pattern, $propertyName)) {
        return TRUE;
      }
      return FALSE;
    }
  }
}
