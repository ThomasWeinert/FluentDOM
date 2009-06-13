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

  private function _isCSSProperty($propertyName) {
    $pattern = '(^-?(?:[a-z]+-)*(?:[a-z]+)$)D';
    if (preg_match($pattern, $propertyName)) {
      return TRUE;
    }
    return FALSE;
  }

  private function _decodeStyleAttribute($styleString) {
    return array();
  }

  private function _encodeStyleAttribute($properties) {
    return '';
  }
}