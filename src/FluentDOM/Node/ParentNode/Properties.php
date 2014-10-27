<?php

namespace FluentDOM\Node\ParentNode {

  trait Properties {

    use Implementation;

    public function __get($name) {
      switch ($name) {
      case 'firstElementChild' :
        return $this->getFirstElementChild();
      case 'lastElementChild' :
        return $this->getLastElementChild();
      }
      return $this->$name;
    }

    public function __set($name, $value) {
      switch ($name) {
      case 'firstElementChild' :
      case 'lastElementChild' :
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