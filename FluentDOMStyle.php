<?php
/**
* FluentDOMStyle extends the FluentDOM class with a function to edit
* the style attribute of html tags
*
* @version $Id: FluentDOM.php 155 2009-06-11 13:08:01Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/

/**
* include the parant class (FluentDOM)
*/
require_once(dirname(__FILE__).'/FluentDOM.php');

/**
* Function to create a new FluentDOMStyle instance
*
* This is a shortcut for "new FluentDOMStyle($source)"
*
* @param mixed $source
* @access public
* @return object FluentDOMStyle
*/
function FluentDOMStyle($content) {
  return new FluentDOMStyle($content);
}

/**
* FluentDOMStyle extends the FluentDOM class with a function to edit
* the style attribute of html tags
*/
class FluentDOMStyle extends FluentDOM {

  /**
  * Pattern to decode the stlye property string
  */
  const STYLE_PATTERN = '((?:^|;)\s*(?P<name>[-\w]+)\s*:\s*(?P<value>[^;]+))';

  /**
  * redefine the _spawn() method to get an new instance of FluentDOMStyle
  *
  * @access protected
  * @return object FluentDOMStyle
  */
  protected function _spawn() {
    return new FluentDOMStyle($this);
  }

  /**
  * get or set CSS values in style attributes
  *
  * @param string | array $property
  * @param NULL | string | Closure $value
  * @access public
  * @return string | object FluentDOMStyle
  */
  public function css($property, $value = NULL) {
    if (is_array($property)) {
      //set list of properties to all elements
      foreach ($this->_array as $node) {
        if ($node instanceof DOMElement) {
          $options = $this->_decodeStyleAttribute($node->getAttribute('style'));
          foreach ($property as $name => $value) {
            if ($this->_isCSSProperty($name)) {
              if (isset($options[$name]) && empty($value)) {
                unset($options[$name]);
              } elseif (!empty($value)) {
                $options[$name] = $value;
              }
            } else {
              throw new InvalidArgumentException('Invalid css property name: '.$property);
            }
          }
          $styleString = $this->_encodeStyleAttribute($options);
          if (empty($styleString) && $node->hasAttribute('style')) {
            $node->removeAttribute('style');
          } elseif (!empty($styleString)) {
            $node->setAttribute('style', $styleString);
          }
        }
      }
    } elseif (is_null($value)) {
      //get value from first DOMElement
      $firstNode = NULL;
      foreach ($this->_array as $node) {
        if ($node instanceof DOMElement) {
          $firstNode = $node;
          break;
        }
      }
      if (empty($firstNode)) {
        return NULL;
      } else {
        $options = $this->_decodeStyleAttribute($firstNode->getAttribute('style'));
        if (isset($options[$property])) {
          return $options[$property];
        }
      }
      return NULL;
    } else {
      //set value to all nodes
      if ($this->_isCSSProperty($property)) {
        foreach ($this->_array as $node) {
          if ($node instanceof DOMElement) {
            $options = $this->_decodeStyleAttribute($node->getAttribute('style'));
            if (is_string($value)) {
              $options[$property] = $value;
            } elseif ($this->_isCallback($value)) {
              $options[$property] = call_user_func(
                $value,
                $node,
                $property,
                empty($options[$property]) ? '' : $options[$property]
              );
            }
            $styleString = $this->_encodeStyleAttribute($options);
            if (empty($styleString) && $node->hasAttribute('style')) {
              $node->removeAttribute('style');
            } elseif (!empty($styleString)) {
              $node->setAttribute('style', $styleString);
            }
          }
        }
      } else {
        throw new InvalidArgumentException('Invalid css property name: '.$property);
      }
    }
  }

  /**
  * check if string is an valid css property name
  *
  * @param string $propertyName
  * @access private
  * @return boolean
  */
  private function _isCSSProperty($propertyName) {
    $pattern = '(^-?(?:[a-z]+-)*(?:[a-z]+)$)D';
    if (preg_match($pattern, $propertyName)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
  * decode style attribute to css properties array
  *
  * @param string $styleString
  * @access private
  * @return array
  */
  private function _decodeStyleAttribute($styleString) {
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
  * encode css options array for the style string
  *
  * @param array $properties
  * @access private
  * @return string
  */
  private function _encodeStyleAttribute($properties) {
    $result = '';
    if (is_array($properties) && count($properties) > 0) {
      uksort($properties, array($this, '_compareCSSProperties'));
      foreach ($properties as $name => $value) {
        $result .= ' '.$name.': '.$value.';';
      }
    }
    return substr($result, 1);
  }
  
  /**
  * compare to css property names
  *
  * @param string $item1
  * @param string $item2
  * @access private
  * @return integer
  */
  private function _compareCSSProperties($item1, $item2) {
    $levels1 = substr_count($item1, '-');
    $levels2 = substr_count($item2, '-');
    if ($levels1 == $levels2) {
      return strnatcasecmp($item1, $item2);
    } else {
      return $levels1 > $levels2 ? 1 : -1;
    }
    return 0;
  }
}