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

    private function getParentProperty($name) {
      static $useParentMethod = NULL;
      if (NULL === $useParentMethod) {
         $useParentMethod = method_exists(get_parent_class($this), '__get');
      }
      return $useParentMethod ? parent::__get($name) : $this->$name;
    }

    private function setParentProperty($name, $value) {
      static $useParentMethod = NULL;
      if (NULL === $useParentMethod) {
         $useParentMethod = method_exists(get_parent_class($this), '__set');
      }
      if ($useParentMethod) {
        parent::__set($name, $value);
      } else {
        $this->$name = $value;
      }
    }
  }
}