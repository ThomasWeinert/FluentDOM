<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Creator {

  use FluentDOM\Appendable;
  use FluentDOM\DOM\Element;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Utility\Constraints;

  /**
   * Internal class for the FluentDOM\Creator, please do not use directly
   * @internal
   */
  class Nodes implements Appendable, \OuterIterator {

    private iterable $_iterable;

    private ?\Closure $_map;

    private ?\Iterator $_iterator = NULL;

    public function __construct(iterable $iterable, callable $map = NULL) {
      $this->_iterable = $iterable;
      $this->_map = Constraints::filterCallable($map);
    }

    public function getInnerIterator(): \Iterator {
      if (NULL === $this->_iterator) {
        if ($this->_iterable instanceof \Iterator) {
          $this->_iterator = $this->_iterable;
        } elseif (is_array($this->_iterable)) {
          $this->_iterator = new \ArrayIterator($this->_iterable);
        } else {
          /** @noinspection PhpParamsInspection */
          $this->_iterator = new \IteratorIterator($this->_iterable);
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

    public function key(): string|int|float {
      return $this->getInnerIterator()->key();
    }

    public function current(): mixed {
      if (NULL !== $this->_map) {
        $map = $this->_map;
        return $map(
          $this->getInnerIterator()->current(),
          $this->getInnerIterator()->key()
        );
      }
      return $this->getInnerIterator()->current();
    }

    public function valid(): bool {
      return $this->getInnerIterator()->valid();
    }

    /**
     * @throws UnattachedNode
     */
    public function appendTo(Element $parentNode): void {
      $parentNode->append(...$this);
    }
  }
}
