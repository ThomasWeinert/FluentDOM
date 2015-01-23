<?php
/**
 * An iterator that calls a map function for the current value.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */
namespace FluentDOM\Iterators {

  /**
   * An iterator that calls a map function for the current value before returning it.
   */
  class MapIterator extends \IteratorIterator {

    protected $_position  = 0;

    /**
     * @var callable
     */
    private $_callback = NULL;

    /**
     * @param \Traversable $traversable
     * @param callable $callback
     */
    public function __construct(\Traversable $traversable, callable $callback) {
      parent::__construct($traversable);
      $this->_callback = $callback;
    }

    /**
     * @return mixed
     */
    public function current() {
      $callback = $this->_callback;
      return $callback(parent::current(), parent::key());
    }
  }
}
