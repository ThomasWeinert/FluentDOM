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

namespace FluentDOM\Utility {


  /**
   * Utility class that handles a list of namespace definitions.
   */
  class Namespaces implements NamespaceResolver, \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * @var string[]
     */
    private array $_namespaces = [];

    private array $_reserved = [
      'xml' => 'http://www.w3.org/XML/1998/namespace',
      'xmlns' => 'http://www.w3.org/2000/xmlns/'
    ];

    private array $_stash = [];

    public function __construct(iterable $namespaces = NULL) {
      if (NULL !== $namespaces) {
        $this->assign($namespaces);
      }
    }

    public function resolveNamespace(string $prefix): ?string {
      return $this[empty($prefix) ? '#default' : $prefix];
    }

    public function isReservedPrefix(string $prefix): bool {
      return \array_key_exists($prefix, $this->_reserved);
    }

    /**
     * @param string $offset
     */
    public function offsetExists(mixed $offset): bool {
      return \array_key_exists($offset, $this->_reserved) || \array_key_exists($offset, $this->_namespaces);
    }

    /**
     * @param string $offset
     * @param string $value
     * @throws \LogicException
     */
    public function offsetSet(mixed $offset, mixed $value): void {
      $offset = $this->validatePrefix($offset);
      if (isset($this->_reserved[$offset])) {
        throw new \LogicException(
          \sprintf('Can not register reserved namespace prefix "%s".', $offset)
        );
      }
      $this->_namespaces[$offset] = $value;
    }

    /**
     * @param string $offset
     * @throws \LogicException
     */
    public function offsetGet(mixed $offset): ?string {
      $offset = $this->validatePrefix($offset);
      if (\array_key_exists($offset, $this->_reserved)) {
        return $this->_reserved[$offset];
      }
      if (\array_key_exists($offset, $this->_namespaces)) {
        return $this->_namespaces[$offset];
      }
      if ($offset === '#default') {
        return '';
      }
      throw new \LogicException(
        \sprintf('Unknown namespace prefix "%s".', $offset)
      );
    }

    /**
     * @param string $offset
     */
    public function offsetUnset(mixed $offset): void {
      $offset = $this->validatePrefix($offset);
      if (\array_key_exists($offset, $this->_namespaces)) {
        unset($this->_namespaces[$offset]);
      }
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      return new \ArrayIterator($this->_namespaces);
    }

    /**
     * Store current status on the stash
     */
    public function store(): void {
      $this->_stash[] = $this->_namespaces;
    }

    /**
     * Restore last stashed status from the stash
     */
    public function restore(): void {
      $this->_namespaces = \array_pop($this->_stash);
    }

    public function count(): int {
      return \count($this->_namespaces);
    }

    /**
     * @throws \LogicException
     */
    public function assign(iterable $namespaces): void {
      $this->_namespaces = [];
      foreach ($namespaces as $prefix => $namespaceURI) {
        $this->offsetSet($prefix, $namespaceURI);
      }
    }

    private function validatePrefix(string $prefix): string {
      return empty($prefix) ? '#default' : $prefix;
    }
  }
}
