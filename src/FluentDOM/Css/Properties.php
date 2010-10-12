<?php

class FluentDOMCssProperties implements ArrayAccess, IteratorAggregate, Countable {

  /**
  * Pattern to decode the style property string
  */
  const STYLE_PATTERN = '((?:^|;)\s*(?P<name>[-\w]+)\s*:\s*(?P<value>[^;]+))';

  /**
  * property storage
  *
  * @var array
  */
  private $_properties = array();

  public function __construct($styleString = '') {
    $this->setStyleString($styleString);
  }

  public function __toString() {
    return $this->getStyleString();
  }

  /**
  * Decode style attribute to the css properties array.
  *
  * @param string $styleString
  */
  public function setStyleString($styleString) {
    $this->_properties = array();
    if (!empty($styleString)) {
      $matches = array();
      if (preg_match_all(self::STYLE_PATTERN, $styleString, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
          if (isset($match['name']) &&
              isset($match['value']) &&
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
  public function getStyleString() {
    $result = '';
    if (is_array($this->_properties) && count($this->_properties) > 0) {
      uksort($this->_properties, array($this, '_compare'));
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
  * @return ArrayIterator
  */
  public function getIterator() {
    return new ArrayIterator($this->_properties);
  }

  /**
  * Get the property count of the first selected node
  *
  * @see Countable::count()
  * @return integer
  */
  public function count() {
    return count($this->_properties);
  }

  /**
  * Allow to use isset() and array syntax to check if a css property is set on
  * the first matched node.
  *
  * @see ArrayAccess::offsetExists()
  */
  public function offsetExists($name) {
    return isset($this->_properties[$name]);
  }

  /**
  * Allow to use array syntax to read a css property value from first matched node.
  *
  * @see ArrayAccess::offsetGet()
  * @param string $name
  * @return $value
  */
  public function offsetGet($name) {
    return $this->_properties[$name];
  }

  /**
  * Set a property
  *
  * @see ArrayAccess::offsetSet()
  * @param string $name
  * @param string $value
  */
  public function offsetSet($name, $value) {
    if ($this->_isCSSProperty($name)) {
      if (trim($value) !== '') {
        $this->_properties[$name] = (string)$value;
      } else {
        $this->offsetUnset($name);
      }
    } else {
      throw new InvalidArgumentException('Invalid css property name: '.$name);
    }
  }

  /**
  * Remove a css properties if it is set.
  *
  * @see ArrayAccess::offsetUnset()
  * @param string $name
  */
  public function offsetUnset($name) {
    if (array_key_exists($name, $this->_properties)) {
      unset($this->_properties[$name]);
    }
  }

  /**
  * Compile value argument into a string (it can be an callback)
  *
  * @param string|Callback|Closure $value
  * @param DOMElement $node
  * @param integer $index
  * @param string $currentValue
  * @return string
  */
  public function compileValue($value, $node, $index, $currentValue) {
    if (!is_string($value) &&
        is_callable($value, TRUE)) {
      return (string)call_user_func(
        $value,
        $node,
        $index,
        $currentValue
      );
    }
    return (string)$value;
  }

  /**
  * Compare to css property names by name, browser-prefix and level.
  *
  * @param string $propertyNameOne
  * @param string $propertyNameTwo
  * @return integer
  */
  private function _compare($propertyNameOne, $propertyNameTwo) {
    $propertyOne = $this->_decodeName($propertyNameOne);
    $propertyTwo = $this->_decodeName($propertyNameTwo);
    $propertyOneLevels = count($propertyOne);
    $propertyTwoLevels = count($propertyTwo);
    $maxLevels = ($propertyOneLevels > $propertyTwoLevels)
      ? $propertyOneLevels : $propertyTwoLevels;
    for ($i = 0; $i < $maxLevels; ++$i) {
      if (isset($propertyOne[$i]) &&
          isset($propertyTwo[$i])) {
        $compare = strnatcasecmp(
          $propertyOne[$i],
          $propertyTwo[$i]
        );
        if ($compare != 0) {
          return $compare;
        }
      } else {
        break;
      }
    }
    if ($propertyOneLevels > $propertyTwoLevels) {
      return 1;
    } else {
      return -1;
    }
  }

  /**
  * Decodes the css property name into an compareable array.
  *
  * @return array
  */
  private function _decodeName($propertyName) {
    if (substr($propertyName, 0, 1) == '-') {
      $pos = strpos($propertyName, '-', 1);
      $items = explode('-', substr($propertyName, $pos + 1));
      $items[] = substr($propertyName, 1, $pos);
      return $items;
    } else {
      $items = explode('-', $propertyName);
      return $items;
    }
  }

  /**
  * Check if string is an valid css property name.
  *
  * @param string $propertyName
  * @return boolean
  */
  private function _isCSSProperty($propertyName) {
    $pattern = '(^-?(?:[a-z]+-)*(?:[a-z]+)$)D';
    if (preg_match($pattern, $propertyName)) {
      return TRUE;
    }
    return FALSE;
  }
}