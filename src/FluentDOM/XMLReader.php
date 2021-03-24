<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Node;
  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\Exceptions\InvalidArgument as InvalidArgumentType;
  use FluentDOM\Utility\Namespaces;
  use FluentDOM\Utility\QualifiedName;
  use FluentDOM\Utility\ResourceWrapper;

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
    public function registerNamespace(string $prefix, string $namespaceURI): void {
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
    public function next($name = NULL, string $namespaceURI = NULL, callable $filter = NULL): bool {
      if (NULL !== $name) {
        [$localName, $namespaceURI, $ignoreNamespace] = $this->prepareCondition($name, $namespaceURI);
      } else {
        $ignoreNamespace = NULL === $namespaceURI || '' === $namespaceURI;
        $localName = $name;
        $namespaceURI = '';
      }
      if ($ignoreNamespace && !$filter) {
        return NULL !== $name ? parent::next($name) : parent::next();
      }
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

    /**
     * Move to next node in document, including subtrees. If $name contains
     * a namespace prefix it will be resolved using the registered namespaces.
     *
     * @param NULL|string $name The name of the next node to move to.
     * @param NULL|string $namespaceURI
     * @param callable|NULL $filter
     * @return bool
     */
    public function read(string $name = NULL, string $namespaceURI = NULL, callable $filter = NULL): bool {
      if (NULL !== $name) {
        [$localName, $namespaceURI, $ignoreNamespace] = $this->prepareCondition($name ?? '', $namespaceURI);
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
      }
      if ($filter) {
        while (parent::read()) {
          if ($filter($this)) {
            return TRUE;
          }
        }
        return FALSE;
      }
      return parent::read();
    }

    /**
     * Return attribute by name, resolve namespace prefix if included.
     *
     * @param string $name
     * @return NULL|string
     */
    public function getAttribute($name) {
      [$prefix, $localName] = QualifiedName::split($name);
      if (empty($prefix)) {
        return parent::getAttribute($name);
      }
      return parent::getAttributeNs($localName, $this->_namespaces->resolveNamespace($prefix));
    }

    /**
     * @param \DOMNode $baseNode
     * @return Node|\DOMNode
     * @throws \LogicException
     */
    public function expand($baseNode = NULL) {
      if (NULL !== $baseNode) {
        return parent::expand($baseNode);
      }
      $this->_document = $document = new Document();
      $document->namespaces($this->_namespaces);
      return parent::expand($document);
    }

    /**
     * @param string $name
     * @param string|NULL $namespaceURI
     * @return array
     */
    private function prepareCondition(string $name, string $namespaceURI = NULL): array {
      if (NULL !== $namespaceURI) {
        $localName = $name;
        $namespaceURI = (string)$namespaceURI;
        $ignoreNamespace = FALSE;
      } else {
        [$prefix, $localName] = QualifiedName::split($name);
        $namespaceURI = $prefix ? $this->_namespaces->resolveNamespace($prefix) : '';
        $ignoreNamespace = ($prefix === FALSE && $namespaceURI === '');
      }
      return [$localName, $namespaceURI, $ignoreNamespace];
    }

    /**
     * @param resource $stream
     * @param string|NULL $encoding
     * @param int $options
     * @return bool
     * @throws InvalidArgumentType
     */
    public function attachStream($stream, string $encoding = NULL, int $options = 0): bool {
      if (!\is_resource($stream)) {
        throw new InvalidArgument('stream', 'resource');
      }
      [$uri, $context] = ResourceWrapper::createContext($stream);
      \libxml_set_streams_context($context);
      return NULL !== $this->open($uri, $encoding, $options);
    }
  }
}
