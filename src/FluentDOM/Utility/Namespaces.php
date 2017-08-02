<?php
/**
 * Utility class that handles a list of namespace definitions.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Utility {


  /**
   * Utility class that handles a list of namespace definitions.
   */
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

    private $_stash = [];

    /**
     * Namespaces constructor.
     * @param NULL|array|\Traversable $namespaces
     */
    public function __construct($namespaces = NULL) {
      if (NULL !== $namespaces) {
        $this->assign($namespaces);
      }
    }

    /**
     * @param string $prefix
     * @return string|NULL
     */
    public function resolveNamespace(string $prefix) {
      return $this[empty($prefix) ? '#default' : $prefix];
    }

    /**
     * @param string $prefix
     * @return bool
     */
    public function isReservedPrefix($prefix): bool {
      return array_key_exists($prefix, $this->_reserved);
    }

    /**
     * @param string $prefix
     * @return bool
     */
    public function offsetExists($prefix): bool {
      return array_key_exists($prefix, $this->_reserved) || array_key_exists($prefix, $this->_namespaces);
    }

    /**
     * @param string $prefix
     * @param string $namespaceURI
     * @throws \LogicException
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
     * @return string|NULL
     * @throws \LogicException
     */
    public function offsetGet($prefix) {
      $prefix = $this->validatePrefix($prefix);
      if (array_key_exists($prefix, $this->_reserved)) {
        return $this->_reserved[$prefix];
      }
      if (array_key_exists($prefix, $this->_namespaces)) {
        return $this->_namespaces[$prefix];
      }
      if ($prefix === '#default') {
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
     * Store current status on the stash
     */
    public function store() {
      $this->_stash[] = $this->_namespaces;
    }

    /**
     * Restore last stashed status from the stash
     */
    public function restore() {
      $this->_namespaces = array_pop($this->_stash);
    }

    /**
     * @return int
     */
    public function count():int {
      return count($this->_namespaces);
    }

    /**
     * @param array|\Traversable $namespaces
     * @throws \LogicException
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
    private function validatePrefix($prefix): string {
      return empty($prefix) ? '#default' : $prefix;
    }
  }
}