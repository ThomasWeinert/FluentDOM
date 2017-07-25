<?php

namespace FluentDOM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Node;
  use FluentDOM\Utility\Namespaces;
  use FluentDOM\Utility\QualifiedName;

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
     * @param string $namespaceURI
     * @throws \LogicException
     */
    public function registerNamespace($prefix, $namespaceURI) {
      $this->_namespaces[$prefix] = $namespaceURI;
    }

    /**
     * Positions cursor on the next node skipping all subtrees. If $name contains
     * a namespace prefix it will be resolved using the registered namespaces.
     *
     * @param NULL|string $name The name of the next node to move to.
     * @param NULL|string $namespaceURI
     * @param callable|NULL $filter
     * @return bool
     */
    public function next($name = NULL, $namespaceURI = NULL, callable $filter = NULL) {
      if (isset($name)) {
        list($localName, $namespaceURI, $ignoreNamespace) = $this->prepareCondition($name, $namespaceURI);
      } else {
        $ignoreNamespace = empty($namespaceURI);
        $localName = $name;
        $namespaceURI = '';
      }
      if ($ignoreNamespace && !$filter) {
        return isset($name) ? parent::next($name) : parent::next();
      } else {
        $found = empty($localName) ? parent::next() : parent::next($localName);
        while ($found) {
          if (
            ($ignoreNamespace || $this->namespaceURI === $namespaceURI) &&
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
     * @param NULL|string $name The name of the next node to move to.
     * @param NULL|string $namespaceURI
     * @param callable|NULL $filter
     * @return bool
     */
    public function read($name = NULL, $namespaceURI = NULL, callable $filter = NULL) {
      if (isset($name)) {
        list($localName, $namespaceURI, $ignoreNamespace) = $this->prepareCondition($name, $namespaceURI);
        while (parent::read()) {
          if (
            $this->nodeType === XML_ELEMENT_NODE &&
            $this->localName === $localName &&
            (
              ($ignoreNamespace || ($this->namespaceURI === $namespaceURI)) &&
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
     * @return Node|\DOMNode
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
     * @param $namespaceURI
     * @return array
     */
    private function prepareCondition($name, $namespaceURI) {
      if (isset($namespaceURI)) {
        $localName = $name;
        $namespaceURI = (string)$namespaceURI;
        $ignoreNamespace = FALSE;
      } else {
        list($prefix, $localName) = QualifiedName::split($name);
        $namespaceURI = $prefix ? $this->_namespaces->resolveNamespace($prefix) : '';
        $ignoreNamespace = ($prefix === FALSE && $namespaceURI === '');
      }
      return [$localName, $namespaceURI, $ignoreNamespace];
    }
  }
}