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

namespace FluentDOM\Loader {

  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\Exceptions\InvalidSource;

  /**
   * Generalized option handling for loaders
   */
  class Options implements \IteratorAggregate, \ArrayAccess {

    public const IS_FILE = 'is_file';
    public const IS_STRING = 'is_string';
    public const ALLOW_FILE = 'allow_file';

    public const LIBXML_OPTIONS = 'libxml';
    public const ENCODING = 'encoding';
    public const FORCE_ENCODING = 'force-encoding';
    public const PRESERVE_WHITESPACE = 'preserve_whitespace';

    public const CB_IDENTIFY_STRING_SOURCE = 'identifyStringSource';

    private array $_options = [
      self::PRESERVE_WHITESPACE => FALSE
    ];
    protected array $_callbacks = [
      self::CB_IDENTIFY_STRING_SOURCE => FALSE
    ];

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
      iterable $options = [],
      array $callbacks = []
    ) {
      if (is_iterable($options)) {
        foreach ($options as $name => $value) {
          $this->offsetSet($name, $value);
        }
      } else {
        throw new InvalidArgument('options', ['array', \Traversable::class]);
      }
      foreach ($callbacks as $name => $callback) {
        $this->setCallback($name, $callback);
      }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setCallback(string $name, callable $callback): void {
      if (!array_key_exists($name, $this->_callbacks)) {
        throw new \InvalidArgumentException(
          \sprintf('Unknown callback specifier "%s".', $name)
        );
      }
      $this->_callbacks[$name] = $callback;
    }

    private function executeCallback(string $name, mixed $default, mixed ...$arguments): mixed {
      $callback = $this->_callbacks[$name];
      if (\is_callable($callback)) {
        return $callback(...$arguments);
      }
      return $default;
    }

    public function getIterator(): \Iterator {
      return new \ArrayIterator($this->_options);
    }

    public function offsetExists(mixed $offset): bool {
      return array_key_exists($offset, $this->_options);
    }

    public function offsetGet(mixed $offset): mixed {
      return $this->_options[$offset] ?? NULL;
    }

    public function offsetSet(mixed $offset, mixed $value): void {
      switch ($offset) {
      case self::IS_STRING :
        if ($value) {
          $this->_options[self::IS_FILE] = FALSE;
          $this->_options[self::ALLOW_FILE] = FALSE;
        }
        break;
      case self::IS_FILE :
        if ($value) {
          $this->_options[self::IS_STRING] = FALSE;
          $this->_options[self::ALLOW_FILE] = TRUE;
        }
        break;
      case self::ALLOW_FILE :
        if (!$value) {
          $this->_options[self::IS_FILE] = FALSE;
        }
        break;
      }
      $this->_options[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void {
      $this->_options[$offset] = NULL;
    }

    public function getSourceType(mixed $source): string {
      if ($this[self::IS_FILE]) {
        return self::IS_FILE;
      }
      if ($this[self::IS_STRING]) {
        return self::IS_STRING;
      }
      $isStringSource = $this->executeCallback(
        self::CB_IDENTIFY_STRING_SOURCE, TRUE, $source
      );
      return $isStringSource ? self::IS_STRING : self::IS_FILE;
    }

    /**
     * @throws InvalidSource
     */
    public function isAllowed(string $sourceType, bool $throwException = TRUE): bool {
      try {
        switch ($sourceType) {
        case self::IS_FILE :
          if (!($this[self::IS_FILE] || $this[self::ALLOW_FILE])) {
            throw new InvalidSource\TypeFile('File source not allowed.');
          }
          break;
        case self::IS_STRING :
          if ($this[self::IS_FILE]) {
            throw new InvalidSource\TypeString('File source expected.');
          }
          break;
        }
      } catch (InvalidSource $e) {
        /** @var InvalidSource\TypeFile|InvalidSource\TypeString $e */
        if ($throwException) {
          throw $e;
        }
        return FALSE;
      }
      return TRUE;
    }
  }
}
