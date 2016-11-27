<?php
/**
 * Provide options for a loader
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2016 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Exceptions\InvalidArgument;

  /**
   * Generalized option handling for loaders
   */
  class Options implements \IteratorAggregate, \ArrayAccess {

    const IS_FILE = 'is_file';
    const ALLOW_FILES = 'allow_files';

    private $_options = [];
    private $_callbacks = [
      'identifyStringSource' => false
    ];

    public function __construct($options = [], $callbacks = []) {
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

    public function setCallback($name, callable $callback) {
      if (!array_key_exists($name, $this->_callbacks)) {
        throw new \InvalidArgumentException(
          sprintf('Unknown callback specifier "%s".', $name)
        );
      }
      $this->_callbacks[$name] = $callback;
    }

    private function executeCallback($name, ...$arguments) {
      if (array_key_exists($name, $this->_callbacks)) {
        $callback = $this->_callbacks[$name];
        if (is_callable($callback)) {
          return NULL;
        } else {
          $callback($arguments);
        }
      } else {
        throw new \InvalidArgumentException(
          sprintf('Unknown callback specifier "%s".', $name)
        );
      }
    }

    public function getIterator() {
      return new \ArrayIterator($this->_options);
    }

    public function offsetExists($offset) {
      return array_key_exists($offset, $this->_options);
    }

    public function offsetGet($offset) {
      return array_key_exists($offset, $this->_options) ? $this->_options[$offset] : NULL;
    }

    public function offsetSet($offset, $value) {
      $this->_options[$offset] = $value;
    }

    public function offsetUnset($offset) {
      $this->_options[$offset] = NULL;
    }

    public function isStringSource($source) {
      $isString = $this->executeCallback('identifyStringSource', $source);
      if ($isString && $this[self::IS_FILE]) {
        // define and throw specific exception
        throw new \LogicException(
          'Given source seems to be an fragment but file expected.'
        );
      }
      return TRUE;
    }

    public function isFile($source) {
      $isFile = (FALSE === $this->executeCallback('identifyStringSource', $source));
      if ($isFile && ($this[self::ALLOW_FILES] || $this[self::IS_FILE])) {
        return TRUE;
      }
      // define and throw specific exception
      throw new \LogicException(
        'Given source seems to be an file but expected a  fragment.'
      );
    }
  }
}
