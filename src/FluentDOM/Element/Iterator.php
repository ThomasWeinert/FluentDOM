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
     * @return \DOMNode
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
     * Move iterator pointer to specified element
     *
     * @param integer $position
     * @throws \InvalidArgumentException
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
        NULL !== $this->_owner->childNodes &&
        NULL !== $this->_owner->childNodes->item($this->_position);
    }

    /**
     * Get child nodes of the current iterator element
     *
     * @throw \UnexpectedValueException
     * @return \RecursiveIterator
     */
    public function getChildren() {
      if ($this->current() instanceof Element) {
        return new self($this->current());
      }
      throw new \UnexpectedValueException(
        'Called '.__METHOD__.' with invalid current element.'
      );
    }

    /**
     * Check if the current iterator element has children
     *
     * @return boolean
     */
    public function hasChildren() {
      return
        $this->valid() &&
        $this->current() instanceof Element &&
        NULL !== $this->current()->childNodes;
    }
  }
}