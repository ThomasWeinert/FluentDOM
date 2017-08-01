<?php

namespace FluentDOM\Creator {

  use FluentDOM\Appendable;
  use FluentDOM\DOM\Element;

  /**
   * Internal class for the FluentDOM\Creator, please do not use directly
   */
  class Nodes implements Appendable, \OuterIterator {

    /**
     * @var array|\Traversable
     */
    private $_traversable;

    /**
     * @var callable|NULL
     */
    private $_map;

    /**
     * @var NULL|\Iterator
     */
    private $_iterator;

    /**
     * @param array|\Traversable $traversable
     * @param callable $map
     */
    public function __construct($traversable, callable $map = NULL) {
      $this->_traversable = $traversable;
      $this->_map = $map;
    }

    /**
     * @return \Iterator
     */
    public function getInnerIterator(): \Iterator {
      if (NULL === $this->_iterator) {
        if ($this->_traversable instanceof \Iterator) {
          $this->_iterator = $this->_traversable;
        } elseif (is_array($this->_traversable)) {
          $this->_iterator = new \ArrayIterator($this->_traversable);
        } else {
          $this->_iterator = ($this->_traversable instanceof \Traversable)
            ? new \IteratorIterator($this->_traversable)
            : new \EmptyIterator();
        }
      }
      return $this->_iterator;
    }

    public function rewind() {
      $this->getInnerIterator()->rewind();
    }

    public function next() {
      $this->getInnerIterator()->next();
    }

    /**
     * @return string|int|float
     */
    public function key() {
      return $this->getInnerIterator()->key();
    }

    /**
     * @return mixed
     */
    public function current() {
      if (NULL !== $this->_map) {
        return call_user_func(
          $this->_map,
          $this->getInnerIterator()->current(),
          $this->getInnerIterator()->key()
        );
      }
      return $this->getInnerIterator()->current();
    }

    /**
     * @return bool
     */
    public function valid(): bool {
      return $this->getInnerIterator()->valid();
    }

    /**
     * @param Element $parent
     * @return Element
     */
    public function appendTo(Element $parent): Element {
      foreach ($this as $item) {
        $parent->append($item);
      }
      return $parent;
    }
  }
}