<?php
/**
 * FluentDOM\Element\Iterator is the Iterator class for FluentDOM\Element objects
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Element {

  use FluentDOM\Element as Element;

  /**
   * FluentDOM\Element\Iterator is the Iterator class for FluentDOM\Element objects
   */
  class Iterator implements \RecursiveIterator, \SeekableIterator {

    /**
     * internal position pointer variable
     * @var integer
     */
    private $_position  = 0;

    /**
     * owner (object) of the iterator
     * @var Element
     */
    private $_owner = NULL;

    /**
     * Remember the owner object (the Element object this iterator iterates)
     *
     * @param Element $owner
     */
    public function __construct(Element $owner) {
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
      return $this->_owner->childNodes->item($this->_position);
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
      return
        $this->_owner->hasChildNodes() &&
        $this->_owner->childNodes->length &&
        is_object($this->_owner->childNodes->item($this->_position));
    }

    /**
     * Get child nodes of the current iterator element
     *
     * @return object Element
     */
    public function getChildren() {
      return new self($this->current());
    }

    /**
     * Check if the current iterator element has children
     *
     * @return object Element
     */
    public function hasChildren() {
      return
        $this->valid() &&
        $this->current() instanceof Element &&
        $this->current()->hasChildNodes();
    }
  }
}