<?php

namespace FluentDOM\Loader\Text\ContentLines {

  class Value implements \IteratorAggregate {

    private $_values = [];

    public function __construct($value) {
      if (!empty($value)) {
        $this->_values = is_array($value) ? $value : [$value];
      }
    }

    public function __toString() {
      return implode(' ', $this->_values);
    }

    public function getIterator() {
      return new \ArrayIterator($this->_values);
    }
  }
}