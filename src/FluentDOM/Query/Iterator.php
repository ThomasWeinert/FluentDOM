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

    use \FluentDOM\IteratorSeek;

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
     * @return \DOMNode
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
     */
    public function rewind() {
      $this->_position = 0;
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
     * @return \RecursiveIterator
     */
    public function getChildren() {
      $query = $this->_owner->spawn();
      $query->push($this->_owner->item($this->_position)->childNodes);
      return new self($query);
    }

    /**
     * Check if the current iterator element has children
     *
     * @return boolean
     */
    public function hasChildren() {
      $item = $this->_owner->item($this->_position);
      return $item->hasChildNodes();
    }
  }
}