<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Serializer\Factory {

  use FluentDOM\Exceptions;
  use FluentDOM\Serializer\Factory as SerializerFactory;

  class Group implements SerializerFactory, \ArrayAccess, \IteratorAggregate, \Countable {

    private $_factories = [];

    public function __construct(array $factories = []) {
      foreach ($factories as $contentType => $factory) {
        $this->offsetSet($contentType, $factory);
      }
    }

    public function createSerializer(string $contentType, \DOMNode $node) {
      $serializer = NULL;
      if ($this->offsetExists($contentType)) {
        $factory = $this->offsetGet($contentType);
        if ($factory instanceof SerializerFactory) {
          $serializer = $factory->createSerializer($contentType, $node);
        } elseif (\is_callable($factory)) {
          $serializer = $factory($contentType, $node);
        }
        if (NULL !== $serializer && !\method_exists($serializer, '__toString')) {
          throw new Exceptions\InvalidSerializer($contentType, \get_class($serializer));
        }
      }
      return $serializer;
    }

    private function normalizeContentType(string $contentType): string {
      return strtolower($contentType);
    }

    public function offsetExists($contentType) {
      $contentType = $this->normalizeContentType($contentType);
      return array_key_exists($contentType, $this->_factories);
    }

    public function offsetSet($contentType, $factory) {
      $contentType = $this->normalizeContentType($contentType);
      if (!($factory instanceOf SerializerFactory || \is_callable($factory))) {
        throw new Exceptions\InvalidArgument(
          'factory', 'FluentDOM\Serializer\Factory, callable'
        );
      }
      $this->_factories[$contentType] = $factory;
    }

    public function offsetGet($contentType) {
      $contentType = $this->normalizeContentType($contentType);
      return $this->_factories[$contentType];
    }

    public function offsetUnset($contentType) {
      $contentType = $this->normalizeContentType($contentType);
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
