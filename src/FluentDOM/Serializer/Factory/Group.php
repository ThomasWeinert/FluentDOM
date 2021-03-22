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

namespace FluentDOM\Serializer\Factory {

  use FluentDOM\Exceptions;
  use FluentDOM\Serializer\Factory as SerializerFactory;
  use FluentDOM\Serializer\StringCast;
  use FluentDOM\Utility\StringCastable;
  use PHPUnit\phpDocumentor\Reflection\DocBlock\Serializer;

  class Group implements SerializerFactory, \ArrayAccess, \IteratorAggregate, \Countable {

    private $_factories = [];

    public function __construct(array $factories = []) {
      foreach ($factories as $contentType => $factory) {
        $this->offsetSet($contentType, $factory);
      }
    }

    public function createSerializer(\DOMNode $node, string $contentType): ?StringCastable {
      $serializer = NULL;
      if ($this->offsetExists($contentType)) {
        $factory = $this->offsetGet($contentType);
        if ($factory instanceof SerializerFactory) {
          $serializer = $factory->createSerializer($node, $contentType);
        } elseif (\is_callable($factory)) {
          $serializer = $factory($node, $contentType);
        }
        if ($serializer instanceof StringCastable) {
          return $serializer;
        }
        if ((NULL !== $serializer) && \method_exists($serializer, '__toString')) {
          return new StringCast($serializer);
        }
        if (NULL !== $serializer) {
          throw new Exceptions\InvalidSerializer($contentType, \get_class($serializer));
        }
      }
      return $serializer;
    }

    private function normalizeContentType(string $contentType): string {
      return strtolower($contentType);
    }

    public function offsetExists($offset): bool {
      $contentType = $this->normalizeContentType($offset);
      return array_key_exists($contentType, $this->_factories);
    }

    public function offsetSet($offset, $value): void {
      $contentType = $this->normalizeContentType($offset);
      if (!($value instanceOf SerializerFactory || \is_callable($value))) {
        throw new Exceptions\InvalidArgument(
          'factory', 'FluentDOM\Serializer\Factory, callable'
        );
      }
      $this->_factories[$contentType] = $value;
    }

    /**
     * @param mixed $offset
     * @return callable|Serializer
     */
    public function offsetGet($offset) {
      $contentType = $this->normalizeContentType($offset);
      return $this->_factories[$contentType];
    }

    public function offsetUnset($offset): void {
      $contentType = $this->normalizeContentType($offset);
      if (array_key_exists($contentType, $this->_factories)) {
        unset($this->_factories[$contentType]);
      }
    }

    public function getIterator(): \Iterator {
      return new \ArrayIterator($this->_factories);
    }

    public function count(): int {
      return \count($this->_factories);
    }
  }
}
