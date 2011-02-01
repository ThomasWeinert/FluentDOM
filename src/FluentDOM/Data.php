<?php

class FluentDOMData {

  private $_node = NULL;

  public function __construct(DOMElement $node) {
    $this->_node = $node;
  }

  public function toArray() {
    $result = array();
    foreach ($this->_node->attributes as $attribute) {
      if (0 === strpos(strtolower($attribute->name), 'data-')) {
        $result[substr($attribute->name, 5)] = $this->decodeValue($attribute->value);
      }
    }
    return $result;
  }

  public function __set($name, $value) {
    $this->_node->setAttribute('data-', $this->encodeValue($value));
  }

  public function __get($name) {
    $name = 'data-'.$name;
    if ($this->_node->hasAttribute($name)) {
      return $this->decodeValue($this->_node->getAttribute($name));
    }
  }

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

  private function encodeValue($value) {
    if (is_object($value) || is_array($value)) {
      return json_encode($value);
    } else {
      return (string)$value;
    }
  }
}