<?php

namespace FluentDOM\DOM\Node\ParentNode {

  trait Properties {

    use Implementation;

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name) {
      switch ($name) {
      case 'firstElementChild' :
        return $this->getFirstElementChild() !== NULL;
      case 'lastElementChild' :
        return $this->getLastElementChild() !== NULL;
      }
      return isset($this->$name);
    }

    /**
     * @param string $name
     * @return \FluentDOM\DOM\Element|NULL
     */
    public function __get(string $name) {
      switch ($name) {
      case 'firstElementChild' :
        return $this->getFirstElementChild();
      case 'lastElementChild' :
        return $this->getLastElementChild();
      }
      return $this->$name;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value) {
      $this->blockReadOnlyProperties($name);
      $this->$name = $value;
    }

    /**
     * @param string $name
     */
    public function __unset(string $name) {
      $this->blockReadOnlyProperties($name);
      unset($this->$name);
    }

    /**
     * @param string $name
     * @throws \BadMethodCallException
     */
    protected function blockReadOnlyProperties(string $name) {
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
    }
  }

}