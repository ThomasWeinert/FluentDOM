<?php
/**
 * Provides an array access to a css style string. It is used to
 * modify the attribute values of style attributes.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */
namespace FluentDOM\Query\Css {

  /**
   * Provides an array access to a css style string. It is used to
   * modify the attribute values of style attributes.
   */
  class Properties implements \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * Pattern to decode the style property string
     */
    const STYLE_PATTERN = '((?:^|;)\s*(?P<name>[-\w]+)\s*:\s*(?P<value>[^;]+))';

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
    public function setStyleString(string $styleString) {
      $this->_properties = [];
      if (!empty($styleString)) {
        $matches = [];
        if (preg_match_all(self::STYLE_PATTERN, $styleString, $matches, PREG_SET_ORDER)) {
          foreach ($matches as $match) {
            if (
              isset($match['name'], $match['value']) &&
              $this->_isCSSProperty($match['name']) &&
              trim($match['value']) !== '') {
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
      if (is_array($this->_properties) && count($this->_properties) > 0) {
        uksort($this->_properties, new PropertyCompare());
        foreach ($this->_properties as $name => $value) {
          if (trim($value) !== '') {
            $result .= ' '.$name.': '.$value.';';
          }
        }
      }
      return (string)substr($result, 1);
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
      return count($this->_properties);
    }

    /**
     * Allow to use isset() and array syntax to check if a css property is set on
     * the first matched node.
     *
     * @see ArrayAccess::offsetExists()
     * @param string $name
     * @return bool
     */
    public function offsetExists($name): bool {
      return isset($this->_properties[$name]);
    }

    /**
     * Allow to use array syntax to read a css property value from first matched node.
     *
     * @see ArrayAccess::offsetGet()
     * @param string $name
     * @return string $value
     */
    public function offsetGet($name): string {
      return $this->_properties[$name];
    }

    /**
     * Set a property
     *
     * @see ArrayAccess::offsetSet()
     * @param string $name
     * @param string $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($name, $value) {
      if ($this->_isCSSProperty($name)) {
        if (trim($value) !== '') {
          $this->_properties[$name] = (string)$value;
        } else {
          $this->offsetUnset($name);
        }
      } else {
        throw new \InvalidArgumentException('Invalid css property name: '.$name);
      }
    }

    /**
     * Remove a css properties if it is set.
     *
     * @see ArrayAccess::offsetUnset()
     * @param string|array $names
     */
    public function offsetUnset($names) {
      if (!is_array($names)) {
        $names = [$names];
      }
      foreach ($names as $property) {
        if (array_key_exists($property, $this->_properties)) {
          unset($this->_properties[$property]);
        }
      }
    }

    /**
     * Compile value argument into a string (it can be an callback)
     *
     * @param string|Callable $value
     * @param \DOMElement $node
     * @param int $index
     * @param string $currentValue
     * @return string
     */
    public function compileValue($value, \DOMElement $node, int $index, string $currentValue = NULL) {
      if (!is_string($value) && is_callable($value, TRUE)) {
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