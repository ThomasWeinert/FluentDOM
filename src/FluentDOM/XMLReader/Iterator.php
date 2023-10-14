<?php /*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */ /*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */ /** @noinspection PhpComposerExtensionStubsInspection */
declare(strict_types=1);

namespace FluentDOM\XMLReader {

  use FluentDOM\Utility\Constraints;
  use FluentDOM\XMLReader;

  /**
   * Class Iterator
   *
   * It will use XMLReader::read() to iterate the nodes and use expand to return each found node as DOM node.
   */
  class Iterator implements \Iterator {

    private XMLReader $_reader;

    private ?string $_name;
    private ?\Closure $_filter;

    private int $_key = -1;

    private ?\DOMNode $_current = NULL;

    /**
     * Iterator constructor.
     */
    public function __construct(
      XMLReader $reader, string $name = NULL, callable $filter = NULL
    ) {
      $this->_reader = $reader;
      $this->_name = $name;
      $this->_filter = Constraints::filterCallable($filter);
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
      while ($reader->read($name, NULL, $filter)) {
        if ($reader->nodeType !== \XMLReader::END_ELEMENT) {
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
