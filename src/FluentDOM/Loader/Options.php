<?php
/**
 * Provide options for a loader
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2016 FluentDOM Contributors
 */

namespace FluentDOM\Loader {

  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\Exceptions\InvalidSource;

  /**
   * Generalized option handling for loaders
   */
  class Options implements \IteratorAggregate, \ArrayAccess {

    const IS_FILE = 'is_file';
    const IS_STRING = 'is_string';
    const ALLOW_FILE = 'allow_file';

    const LIBXML_OPTIONS = 'libxml';
    const ENCODING = 'encoding';
    const FORCE_ENCODING = 'force-encoding';

    const CB_IDENTIFY_STRING_SOURCE = 'identifyStringSource';

    private $_options = [];
    protected $_callbacks = [
      self::CB_IDENTIFY_STRING_SOURCE => FALSE
    ];

    /**
     * @param array|\Traversable|Options $options
     * @param array $callbacks
     * @throws \InvalidArgumentException
     */
    public function __construct($options = [], array $callbacks = []) {
      if (is_array($options)) {
        $this->_options = $options;
      } else if ($options instanceof \Traversable) {
        $this->_options = iterator_to_array($options);
      } else {
        throw new InvalidArgument('options', ['array', \Traversable::class]);
      }
      foreach ($callbacks as $name => $callback) {
        $this->setCallback($name, $callback);
      }
    }

    /**
     * @param string $name
     * @param callable $callback
     * @throws \InvalidArgumentException
     */
    public function setCallback(string $name, callable $callback) {
      if (!array_key_exists($name, $this->_callbacks)) {
        throw new \InvalidArgumentException(
          sprintf('Unknown callback specifier "%s".', $name)
        );
      }
      $this->_callbacks[$name] = $callback;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @param mixed ...$arguments
     * @return mixed
     */
    private function executeCallback(string $name, $default, ...$arguments) {
      $callback = $this->_callbacks[$name];
      if (is_callable($callback)) {
        return $callback(...$arguments);
      }
      return $default;
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      return new \ArrayIterator($this->_options);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool {
      return array_key_exists($offset, $this->_options);
    }

    /**
     * @param mixed $offset
     * @return mixed|NULL
     */
    public function offsetGet($offset) {
      return $this->_options[$offset] ?? NULL;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
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

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
      $this->_options[$offset] = NULL;
    }

    /**
     * @param mixed $source
     * @return string
     */
    public function getSourceType($source): string {
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
     * @param string $sourceType
     * @param bool $throwException
     * @return bool
     * @throws InvalidSource\TypeFile
     * @throws InvalidSource\TypeString
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
