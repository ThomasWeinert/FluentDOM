<?php

namespace FluentDOM\HHVM {

  /**
   * This trait is a workaround for the current implementation of DOMNode properties in
   * HHVM
   *
   * https://github.com/facebook/hhvm/issues/4100
   *
   * @codeCoverageIgnore
   */
  trait Properties {

    private function getParentProperty($name) {
      static $useParentMethod = NULL;
      if (NULL === $useParentMethod) {
         $useParentMethod = method_exists(get_parent_class($this), '__get');
      }
      /** @noinspection PhpUndefinedMethodInspection */
      return $useParentMethod ? parent::__get($name) : $this->$name;
    }

    private function setParentProperty($name, $value) {
      static $useParentMethod = NULL;
      if (NULL === $useParentMethod) {
         $useParentMethod = method_exists(get_parent_class($this), '__set');
      }
      if ($useParentMethod) {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__set($name, $value);
      } else {
        $this->$name = $value;
      }
    }
  }
}