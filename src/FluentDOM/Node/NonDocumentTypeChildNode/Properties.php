<?php

namespace FluentDOM\Node\NonDocumentTypeChildNode {


  trait Properties  {

    use Implementation;

    public function __get($name) {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling();
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling();
      }
      return $this->$name;
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
      $this->$name = $value;
      return TRUE;
    }
  }
}