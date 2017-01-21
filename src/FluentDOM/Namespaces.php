<?php

namespace FluentDOM {

  class Namespaces implements NamespaceResolver, \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * @var array
     */
    private $_namespaces = [];

    /**
     * @var array
     */
    private $_reserved = [
      'xml' => 'http://www.w3.org/XML/1998/namespace',
      'xmlns' => 'http://www.w3.org/2000/xmlns/'
    ];

    /**
     * Namespaces constructor.
     * @param null|array|\Traversable $namespaces
     */
    public function __construct($namespaces = NULL) {
      if (isset($namespaces)) {
        $this->assign($namespaces);
      }
    }

    public function resolveNamespace($prefix) {
      return $this[$prefix];
    }

    public function isReservedPrefix($prefix) {
      return array_key_exists($prefix, $this->_reserved);
    }

    public function offsetExists($prefix) {
      return array_key_exists($prefix, $this->_reserved) || array_key_exists($prefix, $this->_namespaces);
    }

    public function offsetSet($prefix, $namespaceUri) {
      $prefix = $this->validatePrefix($prefix);
      if (isset($this->_reserved[$prefix])) {
        throw new \LogicException(
          sprintf('Can not register reserved namespace prefix "%s".', $prefix)
        );
      }
      $this->_namespaces[$prefix] = $namespaceUri;
    }

    public function offsetGet($prefix) {
      $prefix = $this->validatePrefix($prefix);
      if (array_key_exists($prefix, $this->_reserved)) {
        return $this->_reserved[$prefix];
      } elseif (array_key_exists($prefix, $this->_namespaces)) {
        return $this->_namespaces[$prefix];
      } elseif ($prefix === '#default') {
        return '';
      }
      throw new \LogicException(
        sprintf('Unknown namespace prefix "%s".', $prefix)
      );
    }

    public function offsetUnset($prefix) {
      $prefix = $this->validatePrefix($prefix);
      if (array_key_exists($prefix, $this->_namespaces)) {
        unset($this->_namespaces[$prefix]);
      }
    }

    public function getIterator() {
      return new \ArrayIterator($this->_namespaces);
    }

    public function count() {
      return count($this->_namespaces);
    }

    /**
     * @param array|\Traversable $namespaces
     */
    public function assign($namespaces) {
      $this->_namespaces = [];
      foreach ($namespaces as $prefix => $namespaceUri) {
        $this->offsetSet($prefix, $namespaceUri);
      }
    }

    /**
     * @param string $prefix
     * @return string
     */
    private function validatePrefix($prefix) {
      return empty($prefix) ? '#default' : $prefix;
    }
  }
}