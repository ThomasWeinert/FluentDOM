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

  /**
 * A abstract superclass for index based iterators. The object
 * using this iterator needs to implement \Countable and
 * allow to get the current item by an zero based index position.
   */
  abstract class IndexIterator implements \SeekableIterator {

    /**
     * internal position pointer variable
     */
    protected int $_position  = 0;

    /**
     * owner (object) of the iterator
     */
    private \Countable $_owner;

    public function __construct(\Countable $owner) {
      $this->_owner = $owner;
    }

    /**
     * Return the owner object
     */
    protected function getOwner(): \Countable {
      return $this->_owner;
    }

    /**
     * Get current iterator pointer
     */
    public function key(): int {
      return $this->_position;
    }

    /**
     * Move iterator pointer to next element
     */
    public function next(): void {
      ++$this->_position;
    }

    /**
     * Reset iterator pointer
     */
    public function rewind(): void {
      $this->_position = 0;
    }

    /**
     * Move iterator pointer to specified element
     *
     * @throws \InvalidArgumentException
     */
    public function seek(int $offset): void {
      if ($this->getOwner()->count() > $offset) {
        $this->_position = $offset;
      } else {
        throw new \InvalidArgumentException(
          \sprintf(
            'Unknown position %d, only %d items',
            $offset,
            $this->getOwner()->count()
          )
        );
      }
    }
  }
}
