<?php

namespace FluentDOM\Utility {

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
     * @param NULL|array|\Traversable $namespaces
     */
    public function __construct($namespaces = NULL) {
      if (isset($namespaces)) {
        $this->assign($namespaces);
      }
    }

    /**
     * @param string $prefix
     * @return string|NULL
     */
    public function resolveNamespace(string $prefix) {
      return $this[(string)$prefix];
    }

    /**
     * @param string $prefix
     * @return bool
     */
    public function isReservedPrefix($prefix) {
      return array_key_exists($prefix, $this->_reserved);
    }

    /**
     * @param string $prefix
     * @return bool
     */
    public function offsetExists($prefix) {
      return array_key_exists($prefix, $this->_reserved) || array_key_exists($prefix, $this->_namespaces);
    }

    /**
     * @param string $prefix
     * @param string $namespaceURI
     * @return bool
     */
    public function offsetSet($prefix, $namespaceURI) {
      $prefix = $this->validatePrefix($prefix);
      if (isset($this->_reserved[$prefix])) {
        throw new \LogicException(
          sprintf('Can not register reserved namespace prefix "%s".', $prefix)
        );
      }
      $this->_namespaces[$prefix] = $namespaceURI;
    }

    /**
     * @param string $prefix
     * @return string
     */
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

    /**
     * @param string $prefix
     */
    public function offsetUnset($prefix) {
      $prefix = $this->validatePrefix($prefix);
      if (array_key_exists($prefix, $this->_namespaces)) {
        unset($this->_namespaces[$prefix]);
      }
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() {
      return new \ArrayIterator($this->_namespaces);
    }

    /**
     * @return int
     */
    public function count() {
      return count($this->_namespaces);
    }

    /**
     * @param array|\Traversable $namespaces
     */
    public function assign($namespaces) {
      $this->_namespaces = [];
      foreach ($namespaces as $prefix => $namespaceURI) {
        $this->offsetSet($prefix, $namespaceURI);
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