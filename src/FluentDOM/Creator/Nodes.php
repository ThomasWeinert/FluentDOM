<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Creator {

  use FluentDOM\Appendable;
  use FluentDOM\DOM\Element;
  use FluentDOM\Exceptions\UnattachedNode;

  /**
   * Internal class for the FluentDOM\Creator, please do not use directly
   */
  class Nodes implements Appendable, \OuterIterator {

    /**
     * @var array|\Traversable
     */
    private $_iterable;

    /**
     * @var callable|NULL
     */
    private $_map;

    /**
     * @var NULL|\Iterator
     */
    private $_iterator;

    /**
     * @param iterable $iterable
     * @param callable|NULL $map
     */
    public function __construct(iterable $iterable, callable $map = NULL) {
      $this->_iterable = $iterable;
      $this->_map = $map;
    }

    /**
     * @return \Iterator
     */
    public function getInnerIterator(): \Iterator {
      if (NULL === $this->_iterator) {
        if ($this->_iterable instanceof \Iterator) {
          $this->_iterator = $this->_iterable;
        } elseif (\is_array($this->_iterable)) {
          $this->_iterator = new \ArrayIterator($this->_iterable);
        } else {
          $this->_iterator = (NULL !== $this->_iterable)
            ? new \IteratorIterator($this->_iterable)
            : new \EmptyIterator();
        }
      }
      return $this->_iterator;
    }

    public function rewind(): void {
      $this->getInnerIterator()->rewind();
    }

    public function next(): void {
      $this->getInnerIterator()->next();
    }

    /**
     * @return string|int|float
     */
    public function key() {
      return $this->getInnerIterator()->key();
    }

    /**
     * @return mixed
     */
    public function current() {
      if (NULL !== $this->_map) {
        $map = $this->_map;
        return $map(
          $this->getInnerIterator()->current(),
          $this->getInnerIterator()->key()
        );
      }
      return $this->getInnerIterator()->current();
    }

    /**
     * @return bool
     */
    public function valid(): bool {
      return $this->getInnerIterator()->valid();
    }

    /**
     * @param Element $parentNode
     */
    public function appendTo(Element $parentNode): void {
      try {
        $parentNode->append(...$this);
      } catch (UnattachedNode $e) {
      }
    }
  }
}
