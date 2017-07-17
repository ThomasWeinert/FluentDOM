<?php

namespace FluentDOM {

  class XMLReader extends \XMLReader {

    /**
     * @var Namespaces
     */
    private $_namespaces;

    /**
     * Store last used document to avoid early GC
     * @var Document
     */
    private $_document;

    public function __construct() {
      $this->_namespaces = new Namespaces();
    }

    /**
     * register a namespace prefix for the xml reader, it will be used in
     * next() and other methods with a tag name argument
     *
     * @param string $prefix
     * @param string $namespace
     * @throws \LogicException
     */
    public function registerNamespace($prefix, $namespace) {
      $this->_namespaces[$prefix] = $namespace;
    }

    /**
     * Positions cursor on the next node skipping all subtrees. If $name contains
     * a namespace prefix it will be resolved using the registered namespaces.
     *
     * @param null|string $name The name of the next node to move to.
     * @param null|string $namespaceUri
     * @param callable|null $filter
     * @return bool
     */
    public function next($name = NULL, $namespaceUri = NULL, callable $filter = NULL) {
      if (isset($name)) {
        list($localName, $namespaceUri, $ignoreNamespace) = $this->prepareCondition($name, $namespaceUri);
      } else {
        $ignoreNamespace = empty($namespaceUri);
        $localName = $name;
        $namespaceUri = '';
      }
      if ($ignoreNamespace && !$filter) {
        return isset($name) ? parent::next($name) : parent::next();
      } else {
        $found = empty($localName) ? parent::next() : parent::next($localName);
        while ($found) {
          if (
            ($ignoreNamespace || $this->namespaceURI === $namespaceUri) &&
            (!$filter || $filter($this))
          ) {
            return TRUE;
          }
          $found = empty($localName) ? parent::next() : parent::next($localName);
        }
        return FALSE;
      }
    }

    /**
     * Move to next node in document, including subtrees. If $name contains
     * a namespace prefix it will be resolved using the registered namespaces.
     *
     * @param null|string $name The name of the next node to move to.
     * @param null|string $namespaceUri
     * @param callable|null $filter
     * @return bool
     */
    public function read($name = NULL, $namespaceUri = NULL, callable $filter = NULL) {
      if (isset($name)) {
        list($localName, $namespaceUri, $ignoreNamespace) = $this->prepareCondition($name, $namespaceUri);
        while (parent::read()) {
          if (
            $this->nodeType === XML_ELEMENT_NODE &&
            $this->localName === $localName &&
            (
              ($ignoreNamespace || ($this->namespaceURI === $namespaceUri)) &&
              (!$filter || $filter($this))
            )
          ) {
            return TRUE;
          }
        }
        return FALSE;
      } elseif ($filter) {
        while (parent::read()) {
          if ($filter($this)) {
            return TRUE;
          }
        }
        return FALSE;
      } else {
        return parent::read();
      }
    }

    /**
     * Return attribute by name, resolve namespace prefix if included.
     *
     * @param string $name
     * @return NULL|string
     */
    public function getAttribute($name) {
      list($prefix, $localName) = QualifiedName::split($name);
      if (empty($prefix)) {
        return parent::getAttribute($name);
      } else {
        return parent::getAttributeNs($localName, $this->_namespaces->resolveNamespace($prefix));
      }
    }

    /**
     * @param \DOMNode $baseNode
     * @return \DOMNode
     */
    public function expand($baseNode = NULL) {
      if (isset($baseNode)) {
        return parent::expand($baseNode);
      } else {
        $this->_document = $document = new Document();
        $document->namespaces($this->_namespaces);
        return parent::expand($document);
      }
    }

    /**
     * @param $name
     * @param $namespaceUri
     * @return array
     */
    private function prepareCondition($name, $namespaceUri) {
      if (isset($namespaceUri)) {
        $localName = $name;
        $namespaceUri = (string)$namespaceUri;
        $ignoreNamespace = FALSE;
      } else {
        list($prefix, $localName) = QualifiedName::split($name);
        $namespaceUri = $prefix ? $this->_namespaces->resolveNamespace($prefix) : '';
        $ignoreNamespace = ($prefix === FALSE && $namespaceUri === '');
      }
      return [$localName, $namespaceUri, $ignoreNamespace];
    }
  }
}