<?php

namespace FluentDOM\DOM\Node\NonDocumentTypeChildNode {

  use FluentDOM\DOM\HHVM\Properties as HHVMProperties;

  trait Properties {

    use Implementation, HHVMProperties;

    public function __get($name) {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling();
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling();
      }
      return $this->getParentProperty($name);
    }

    public function __set($name, $value) {
      switch ($name) {
      case 'nextElementSibling' :
      case 'previousElementSibling' :
        throw new \BadMethodCallException(
          sprintf(
            'Can not write readonly property %s::$%s.',
            get_class($this), $name
          )
        );
      }
      $this->setParentProperty($name, $value);
    }
  }
}