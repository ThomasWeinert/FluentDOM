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
    public function valid(): bool {
      return \is_object($this->getOwner()->item($this->_position));
    }

    /**
     * Get current iterator element
     *
     * @return \DOMNode|NULL
     */
    public function current(): ?\DOMNode {
      return $this->getOwner()->item($this->_position);
    }

    /**
     * Get children of the current iterator element
     *
     * @return self
     */
    public function getChildren(): self {
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
    public function hasChildren(): bool {
      $item = $this->getOwner()->item($this->_position);
      return $item ? $item->hasChildNodes() : FALSE;
    }
  }
}
