<?php
/**
 * FluentDOM\Query\Iterator is the Iterator class for FluentDOM\Query objects
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Query {

  use FluentDOM\Query as Query;

  /**
   * FluentDOM\Query\Iterator is the Iterator class for FluentDOM\Query objects
   */
  class Iterator implements \RecursiveIterator, \SeekableIterator {

    /**
     * internal position pointer variable
     * @var integer
     */
    private $_position  = 0;

    /**
     * owner (object) of the iterator
     * @var Query
     */
    private $_owner = NULL;

    /**
     * Remember the owner object (the Query object this iterator interates)
     *
     * @param Query $owner
     */
    public function __construct(Query $owner) {
      $this->_owner = $owner;
    }

    /*
   * Interface - Iterator, SeekableIterator
   */

    /**
     * Get current iterator element
     *
     * @return object DOMNode
     */
    public function current() {
      return $this->_owner->item($this->_position);
    }

    /**
     * Get current iterator pointer
     *
     * @return integer
     */
    public function key() {
      return $this->_position;
    }

    /**
     * Move iterator pointer to next element
     *
     * @return void
     */
    public function next() {
      ++$this->_position;
    }

    /**
     * Reset iterator pointer
     *
     * @return void
     */
    public function rewind() {
      $this->_position = 0;
    }

    /**
     * Move iterator pointer to specified element
     *
     * @param integer $position
     * @throws \InvalidArgumentException
     * @return void
     */
    public function seek($position) {
      if (count($this->_owner) > $position) {
        $this->_position = $position;
      } else {
        throw new \InvalidArgumentException('Unknown position');
      }
    }

    /**
     * Check if current iterator pointer contains a valid element
     *
     * @return boolean
     */
    public function valid() {
      return is_object($this->_owner->item($this->_position));
    }

    /**
     * Get children of the current iterator element
     *
     * @return object Query
     */
    public function getChildren() {
      $query = $this->_owner->spawn();
      $query->push($this->_owner->item($this->_position)->childNodes);
      return new self($query);
    }

    /**
     * Check if the current iterator element has children
     *
     * @return object Query
     */
    public function hasChildren() {
      $item = $this->_owner->item($this->_position);
      return $item->hasChildNodes();
    }
  }
}