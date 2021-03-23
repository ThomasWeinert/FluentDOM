<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Utility\Iterators {

  /**
   * An iterator that calls a map function for the current value before returning it.
   */
  class MapIterator extends \IteratorIterator {

    /**
     * @var callable
     */
    private $_callback;

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
