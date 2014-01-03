<?php

namespace FluentDOM {

  class Loaders implements \IteratorAggregate {

    private $_list = array();

    public function __construct($list = NULL) {
      if (is_array($list) || $list instanceOf Loaders) {
        foreach ($list as $loader) {
          $this->add($loader);
        }
      }
    }

    public function add($loader) {
      $this->_list[spl_object_hash($loader)] = $loader;
    }

    public function remove($loader) {
      $key = spl_object_hash($loader);
      if (isset($this->_list[$key])) {
        unset($this->_list[$key]);
      }
    }

    public function getIterator() {
      return new \ArrayIterator($this->_list);
    }
  }
}