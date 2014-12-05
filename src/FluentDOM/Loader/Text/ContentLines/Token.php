<?php

namespace FluentDOM\Loader\Text\ContentLines {

  /**
   * @property-read string $name
   * @property-read string $value
   * @property-read string $type
   * @property-read array $parameters
   */
  class Token {

    private $_name = '';
    private $_value = '';
    private $_type = FALSE;
    private $_parameters = [];

    public function __construct($name, $value) {
      $this->_name = $name;
      $this->_value = $value;
    }

    public function __isset($name) {
      return isset($this->{'_'.$name});
    }

    public function __get($name) {
      if (isset($this->{'_'.$name})) {
        return $this->{'_'.$name};
      }
      throw new \LogicException('Property does not exists');
    }

    public function __set($name, $value) {
      throw new \LogicException('Can not write properties on this object');
    }


    public function __unset($name) {
      throw new \LogicException('Can not write properties on this object');
    }

    public function add($name, $value) {
      switch ($name) {
      case 'VALUE' :
        $this->_type = $value;
        return;
      default :
        $this->_parameters[$name] = $value;
      }
    }
  }
}