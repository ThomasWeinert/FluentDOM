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

namespace FluentDOM\XMLReader {

  use FluentDOM\XMLReader;

  /**
   * Class Iterator
   *
   * It will use XMLReader::read() to iterate the nodes and use expand to return each found node as DOM node.
   */
  class Iterator implements \Iterator {

    /**
     * @var XMLReader
     */
    private $_reader;
    /**
     * @var string|NULL
     */
    private $_name;
    /**
     * @var callable
     */
    private $_filter;

    /**
     * @var int
     */
    private $_key = -1;

    /**
     * @var NULL|\DOMNode
     */
    private $_current;

    /**
     * Iterator constructor.
     *
     * @param XMLReader $reader
     * @param NULL|string $name tag name filter
     * @param callable|NULL $filter
     */
    public function __construct(
      XMLReader $reader, $name = NULL, callable $filter = NULL
    ) {
      $this->_reader = $reader;
      $this->_name = $name;
      $this->_filter = $filter;
    }

    /**
     * Throw an exception if rewind() is called after next()
     *
     * @throws \LogicException
     */
    public function rewind(): void {
      if ($this->_key >= 0) {
        throw new \LogicException(\sprintf('%s is not a seekable iterator', __CLASS__));
      }
      $this->next();
    }

    public function next(): void {
      if ($this->move($this->_reader, $this->_name, $this->_filter)) {
        $this->_current = (NULL === $this->_current)
          ? $this->_reader->expand()
          : $this->_reader->expand($this->_current->ownerDocument);
        $this->_key++;
      } else {
        $this->_current = NULL;
      }
    }

    /**
     * @param XMLReader $reader
     * @param string|NULL $name
     * @param callable|NULL $filter
     * @return bool
     */
    protected function move(
      XMLReader $reader, string $name = NULL, callable $filter = NULL
    ): bool {
      while ($found = $reader->read($name, NULL, $filter)) {
        if ($found && $reader->nodeType !== XMLReader::END_ELEMENT) {
          return TRUE;
        }
      }
      return FALSE;
    }

    /**
     * @return bool
     */
    public function valid(): bool {
      return NULL !== $this->_current;
    }

    /**
     * @return \DOMNode|NULL
     */
    public function current(): ?\DOMNode {
      return $this->_current;
    }

    /**
     * @return int
     */
    public function key(): int {
      return $this->_key;
    }
  }
}
