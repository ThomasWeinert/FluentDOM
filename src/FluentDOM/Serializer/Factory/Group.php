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

namespace FluentDOM\Serializer\Factory {

  use FluentDOM\Exceptions;
  use FluentDOM\Serializer\Serializer;
  use FluentDOM\Serializer\SerializerFactory as SerializerFactory;

  class Group implements SerializerFactory, \ArrayAccess, \IteratorAggregate, \Countable {

    private array $_factories = [];

    public function __construct(array $factories = []) {
      foreach ($factories as $contentType => $factory) {
        $this->offsetSet($contentType, $factory);
      }
    }

    public function createSerializer(\DOMNode $node, string $contentType): ?Serializer {
      $serializer = NULL;
      if ($this->offsetExists($contentType)) {
        $factory = $this->offsetGet($contentType);
        if ($factory instanceof SerializerFactory) {
          $serializer = $factory->createSerializer($node, $contentType);
        } elseif (\is_callable($factory)) {
          $serializer = $factory($node, $contentType);
        }
        if ($serializer instanceof Serializer) {
          return $serializer;
        }
        if ((NULL !== $serializer) && \method_exists($serializer, '__toString')) {
          return (new class($serializer) implements Serializer {
            public function __construct(private object $serializer) {}
            public function __toString(): string { return (string)$this->serializer; }
          });
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

    public function offsetExists(mixed $offset): bool {
      $contentType = $this->normalizeContentType($offset);
      return array_key_exists($contentType, $this->_factories);
    }

    public function offsetSet(mixed $offset, mixed $value): void {
      $contentType = $this->normalizeContentType($offset);
      if (!($value instanceOf SerializerFactory || \is_callable($value))) {
        throw new Exceptions\InvalidArgument(
          'factory', 'FluentDOM\Serializer\Factory, callable'
        );
      }
      $this->_factories[$contentType] = $value;
    }

    public function offsetGet(mixed $offset): callable|SerializerFactory {
      $contentType = $this->normalizeContentType($offset);
      return $this->_factories[$contentType];
    }

    public function offsetUnset(mixed $offset): void {
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
