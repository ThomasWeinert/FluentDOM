<?php
/**
* FluentDOMData is used for the FluentDOM::data property and FluentDOM::data() method, providing an
* interface html5 data properties of a node.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2011 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
*/

/**
* FluentDOMData is used for the FluentDOM::data property and FluentDOM::data() method, providing an
* interface html5 data properties of a node.
*
* @package FluentDOM
*/
class FluentDOMData implements IteratorAggregate {

  /**
  * Attached element node
  *
  * @var DOMelement
  */
  private $_node = NULL;

  /**
  * Create object with attached element node.
  *
  * @param DOMElement $node
  */
  public function __construct(DOMElement $node) {
    $this->_node = $node;
  }

  /**
  * Convert data attributes into an array
  *
  * @return array
  */
  public function toArray() {
    $result = array();
    foreach ($this->_node->attributes as $attribute) {
      if (0 === strpos(strtolower($attribute->name), 'data-')) {
        $result[substr($attribute->name, 5)] = $this->decodeValue($attribute->value);
      }
    }
    return $result;
  }

  /**
  * IteratorAggregate Interface: allow to iterate the data attributes
  *
  * @return ArrayIterator
  */
  public function getIterator() {
    return new ArrayIterator($this->toArray());
  }

  /**
  * Validate if the attached node has the data attribute.
  *
  * @param string $name
  */
  public function __isset($name) {
    return $this->_node->hasAttribute('data-'.$name);
  }

  /**
  * Change a data attribute on the attached node.
  *
  * @param string $name
  * @param mixed $value
  */
  public function __set($name, $value) {
    $this->_node->setAttribute('data-'.$name, $this->encodeValue($value));
  }

  /**
  * Read a data attribute from the attached node.
  *
  * @param string $name
  * @return mixed
  */
  public function __get($name) {
    $name = 'data-'.$name;
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
  public function __unset($name) {
    $this->_node->removeAttribute('data-'.$name);
  }

  /**
  * Decode the attribute value into a php variable/array/object
  *
  * @param string $value
  * @return mixed
  */
  private function decodeValue($value) {
    switch (TRUE) {
      case ($value == 'true') :
        return TRUE;
      case ($value == 'false') :
        return FALSE;
      case (in_array(substr($value, 0, 1), array('{', '['))) :
        if ($json = json_decode($value)) {
          return $json;
        } else {
          return NULL;
        }
      default :
        return $value;
    }
  }

  /**
  * Encode php variable into a string. Array or Objects will be serialized using json encoding.
  * Boolean use the strings yes/no.
  *
  * @param mixed $value
  * @return string
  */
  private function encodeValue($value) {
    if (is_bool($value)) {
      return ($value) ? 'true' : 'false';
    } elseif (is_object($value) || is_array($value)) {
      return json_encode($value);
    } else {
      return (string)$value;
    }
  }
}