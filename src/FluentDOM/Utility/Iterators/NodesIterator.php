<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Utility\Iterators {

  use FluentDOM\Nodes;

  /**
   * FluentDOM\Query\Iterator is the Iterator class for FluentDOM\Query objects
   */
  class NodesIterator extends IndexIterator implements \RecursiveIterator {


    public function __construct(
      private Nodes $nodes
    ) {
      parent::__construct($nodes);
    }
    /**
     * Check if current iterator pointer contains a valid element
     *
     * @return bool
     */
    public function valid(): bool {
      return \is_object($this->nodes->item($this->_position));
    }

    /**
     * Get current iterator element
     *
     * @return \DOMNode|NULL
     */
    public function current(): ?\DOMNode {
      return $this->nodes->item($this->_position);
    }

    /**
     * Get children of the current iterator element
     *
     * @return self
     */
    public function getChildren(): self {
      $query = $this->nodes->spawn();
      $query->push($this->nodes->item($this->_position)->childNodes);
      return new self($query);
    }

    /**
     * Check if the current iterator element has children
     */
    public function hasChildren(): bool {
      $item = $this->nodes->item($this->_position);
      return method_exists($item, 'hasChildNodes') && $item->hasChildNodes();
    }
  }
}
