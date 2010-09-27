<?php
/**
* FluentDOMCss is used for the FluentDOM:css property, providing an array like interface
* to the css properties in the style attribute of the selected nodes(s)
*
* It acts like the FluentDOMStyle::css() method. If you read css properties it uses the first
* selected node. Write actions are applied to all matches element nodes.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2010 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
*/

/**
* FluentDOMCss is used for the FluentDOM:css property, providing an array like interface
* to the css properties in the style attribute of the selected nodes(s)
*
* It acts like the FluentDOMStyle::css() method. If you read css properties it uses the first
* selected node. Write actions are applied to all matches element nodes.
*
* @package FluentDOM
*/
class FluentDOMCss implements ArrayAccess, Countable, IteratorAggregate {

  /**
  * Pattern to decode the style property string
  */
  const STYLE_PATTERN = '((?:^|;)\s*(?P<name>[-\w]+)\s*:\s*(?P<value>[^;]+))';

  /**
  * owner object
  * @var FluentDOM
  */
  private $_fd = NULL;

  private $_properties = array();

  /**
  * Store the FluentDOM instance for later use and decode the style string into an array
  *
  * @param FluentDOM $fd
  */
  public function __construct(FluentDOMCore $fd = NULL, $styleString = '') {
    $this->_fd = $fd;
    $this->_properties = $this->decode($styleString);
  }

  /**
  * Return the current properties as an string for use as an attribute value
  */
  public function __toString() {
    return $this->encode($this->_properties);
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
  * Allow to use array syntax to change a css property value on all matched nodes.
  *
  * @see ArrayAccess::offsetSet()
  * @param string $name
  * @param string $value
  */
  public function offsetSet($name, $value) {
    if (!$this->_isCSSProperty($name)) {
      throw new InvalidArgumentException('Invalid css property name: '.$name);
    }
    if ((string)$value != '') {
      $this->_properties[$name] = $value;
    } elseif (array_key_exists($name, $this->_properties)) {
      unset($this->_properties[$name]);
    }
    if (isset($this->_fd)) {
      foreach ($this->_fd as $index => $node) {
        if ($node instanceof DOMElement) {
          $properties = new self(NULL, $node->getAttribute('style'));
          if (!is_string($value) &&
              is_callable($value, TRUE)) {
            $properties[$name] = call_user_func(
              $value,
              $node,
              $index,
              isset($properties[$name]) ? $properties[$name] : NULL
            );
          } else  {
            $properties[$name] = $value;
          }
          if (count($properties) > 0) {
            $node->setAttribute('style', (string)$properties);
          } else {
            $node->removeAttribute('style');
          }
        }
      }
    }
  }

  /**
  * Allow to use unset and array syntax to remove a css property value on
  * all matched nodes.
  *
  * @see ArrayAccess::offsetUnset()
  * @param string $name
  */
  public function offsetUnset($name) {
    if (array_key_exists($name, $this->_properties)) {
      unset($this->_properties[$name]);
    }
    if (isset($this->_fd)) {
      foreach ($this->_fd as $node) {
        if ($node instanceof DOMElement &&
            $node->hasAttribute('style')) {
          $properties = new self(NULL, $node->getAttribute('style'));
          unset($properties[$name]);
          if (count($properties) > 0) {
            $node->setAttribute('style', (string)$properties);
          } else {
            $node->removeAttribute('style');
          }
        }
      }
    }
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
  * Decode style attribute to css properties array.
  *
  * @param string $styleString
  * @return array
  */
  public function decode($styleString) {
    $result = array();
    if (!empty($styleString)) {
      $matches = array();
      if (preg_match_all(self::STYLE_PATTERN, $styleString, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
          if (isset($match['name']) &&
              $this->_isCSSProperty($match['name']) &&
              !empty($match['value'])) {
            $result[$match['name']] = $match['value'];
          }
        }
      }
    }
    return $result;
  }

  /**
  * Encode css property array for the style string.
  *
  * @param array $properties
  * @return string
  */
  public function encode($properties) {
    $result = '';
    if (is_array($properties) && count($properties) > 0) {
      uksort($properties, array($this, '_compare'));
      foreach ($properties as $name => $value) {
        $result .= ' '.$name.': '.$value.';';
      }
    }
    return substr($result, 1);
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