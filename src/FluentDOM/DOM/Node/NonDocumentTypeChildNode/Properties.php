<?php

namespace FluentDOM\DOM\Node\NonDocumentTypeChildNode {

  trait Properties {

    use Implementation;

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name) {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling() !== NULL;
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling() !== NULL;
      }
      return isset($this->$name);
    }

    /**
     * @param string $name
     * @return \DOMNode|NULL
     */
    public function __get(string $name) {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling();
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling();
      }
      return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \BadMethodCallException
     */
    public function __set(string $name, $value) {
      $this->blockReadOnlyProperties($name);
      $this->$name = $value;
    }

    /**
     * @param string $name
     * @throws \BadMethodCallException
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
      case 'nextElementSibling' :
      case 'previousElementSibling' :
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