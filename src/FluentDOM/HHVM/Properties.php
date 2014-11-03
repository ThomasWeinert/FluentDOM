<?php

namespace FluentDOM\HHVM {

  /**
   * This trait is a workaround for the current implementation of DOMNode properties in
   * HHVM
   *
   * https://github.com/facebook/hhvm/issues/4100
   *
   * @package FluentDOM\HHVM
   */
  trait Properties {

    protected $_isHHVM = NULL;

    private function getParentProperty($name) {
      if (NULL === $this->_isHHVM) {
         $this->_isHHVM = defined('HHVM_VERSION');
      }
      return $this->_isHHVM ? parent::__get($name) : $this->$name;
    }

    private function setParentProperty($name, $value) {
      if (NULL === $this->_isHHVM) {
         $this->_isHHVM = defined('HHVM_VERSION');
      }
      if ($this->_isHHVM) {
        parent::__set($name, $value);
      } else {
        $this->$name = $value;
      }
    }
  }
}