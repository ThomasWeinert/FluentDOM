<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;

  trait HHVMProperties {

    private $_isHHVM = NULL;

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