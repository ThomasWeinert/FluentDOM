<?php
/**
 * FluentDOM\Query\Iterator is the Iterator class for FluentDOM\Query objects
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Utility\Iterators {

  use FluentDOM\Nodes;

  /**
   * FluentDOM\Query\Iterator is the Iterator class for FluentDOM\Query objects
   *
   * @method Nodes getOwner()
   */
  class NodesIterator extends IndexIterator implements \RecursiveIterator {

    /**
     * Check if current iterator pointer contains a valid element
     *
     * @return bool
     */
    public function valid() {
      return is_object($this->getOwner()->item($this->_position));
    }

    /**
     * Get current iterator element
     *
     * @return \DOMNode
     */
    public function current() {
      return $this->getOwner()->item($this->_position);
    }

    /**
     * Get children of the current iterator element
     *
     * @return \RecursiveIterator
     */
    public function getChildren() {
      $owner = $this->getOwner();
      $query = $owner->spawn();
      $query->push($owner->item($this->_position)->childNodes);
      return new self($query);
    }

    /**
     * Check if the current iterator element has children
     *
     * @return bool
     */
    public function hasChildren() {
      $item = $this->getOwner()->item($this->_position);
      return $item->hasChildNodes();
    }
  }
}