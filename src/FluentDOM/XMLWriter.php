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

  use FluentDOM\Utility\Namespaces;
  use FluentDOM\Utility\QualifiedName;

  class XMLWriter extends \XMLWriter {

    /**
     * @var Namespaces
     */
    private $_namespaces;

    /**
     * @var XMLWriter\NamespaceStack
     */
    private $_xmlnsStack;

    public function __construct() {
      $this->_namespaces = new Namespaces();
      $this->_xmlnsStack = new XMLWriter\NamespaceStack();
    }

    /**
     * register a namespace prefix for the xml reader, it will be used in
     * next() and other methods with a tag name argument
     *
     * @param string $prefix
     * @param string $namespaceURI
     * @throws \LogicException
     */
    public function registerNamespace(string $prefix, string $namespaceURI) {
      $this->_namespaces[$prefix] = $namespaceURI;
    }

    /**
     * Add the current namespace configuration as xmlns* attributes to the element node.
     */
    public function applyNamespaces() {
      foreach ($this->_namespaces as $prefix => $namespaceURI) {
        $this->writeAttribute(
          empty($prefix) || $prefix === '#default' ? 'xmlns' : 'xmlns:'.$prefix, $namespaceURI
        );
      }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function startElement($name): bool {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespaceURI = $this->_namespaces->resolveNamespace((string)$prefix);
      return $this->startElementNS((string)$prefix, $localName, $namespaceURI);
    }

    /**
     * @param string $name
     * @param NULL|string $content
     * @return bool
     */
    public function writeElement($name, $content = NULL): bool {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespaceURI = $this->_namespaces->resolveNamespace((string)$prefix);
      return $this->writeElementNS((string)$prefix, $localName, $namespaceURI, $content);
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param string $namespaceURI
     * @return bool
     */
    public function startElementNS($prefix, $name, $namespaceURI): bool {
      $this->_xmlnsStack->push();
      if ($this->_xmlnsStack->isDefined($prefix, $namespaceURI)) {
        $result = parent::startElement(empty($prefix) ? $name : $prefix.':'.$name);
      } else {
        $result = parent::startElementNs(empty($prefix) ? NULL : $prefix, $name, $namespaceURI);
        $this->_xmlnsStack->add($prefix, $namespaceURI);
      }
      return $result;
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param string $uri
     * @param NULL|string $content
     * @return bool
     */
    public function writeElementNS($prefix, $name, $uri, $content = NULL): bool {
      if ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        return parent::writeElement(empty($prefix) ? $name : $prefix.':'.$name, $content);
      }
      return parent::writeElementNs(empty($prefix) ? NULL : $prefix, $name, $uri, $content);
    }

    /**
     * @return bool
     */
    public function endElement(): bool {
      $this->_xmlnsStack->pop();
      return parent::endElement();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function startAttribute($name): bool {
      list($prefix, $localName) = QualifiedName::split($name);
      return $this->startAttributeNS(
        (string)$prefix, $localName, $this->_namespaces->resolveNamespace((string)$prefix)
      );
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function writeAttribute($name, $value): bool {
      list($prefix, $localName) = QualifiedName::split($name);
      return $this->writeAttributeNS(
        (string)$prefix, $localName, $this->_namespaces->resolveNamespace((string)$prefix), $value
      );
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param string $uri
     * @return bool
     */
    public function startAttributeNS($prefix, $name, $uri): bool {
      if (empty($prefix)) {
        return parent::startAttribute($name);
      }
      if ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        return parent::startAttribute($prefix.':'.$name);
      }
      return parent::startAttributeNs($prefix, $name, $uri);
    }

    /**
     * @param string $prefix
     * @param string $localName
     * @param string $uri
     * @param string $content
     * @return bool
     */
    public function writeAttributeNS($prefix, $localName, $uri, $content): bool {
      if ((empty($prefix) && $localName === 'xmlns') || $prefix === 'xmlns') {
        $namespacePrefix = empty($prefix) ? '' : $localName;
        $namespaceURI = $content;
        if (!$this->_xmlnsStack->isDefined($namespacePrefix, $namespaceURI)) {
          $result = parent::writeAttribute(empty($prefix) ? 'xmlns' : 'xmlns:'.$localName, $namespaceURI);
          $this->_xmlnsStack->add($namespacePrefix, $namespaceURI);
          return $result;
        }
        return FALSE;
      }
      if (empty($prefix)) {
        return parent::writeAttribute($localName, $content);
      }
      if ($this->_xmlnsStack->isDefined($prefix, $uri)) {
        return parent::writeAttribute($prefix.':'.$localName, $content);
      }
      return parent::writeAttributeNs($prefix, $localName, $uri, $content);
    }

    /**
     * Write a DOM node
     *
     * @param \DOMNode|\DOMNode[]|iterable $nodes
     * @param int $maximumDepth
     */
    public function collapse($nodes, int $maximumDepth = 1000): void {
      if ($maximumDepth <= 0) {
        return;
      }
      if ($nodes instanceof \DOMNode) {
        $this->collapseNode($nodes, $maximumDepth);
      } elseif (is_iterable($nodes)) {
        foreach ($nodes as $childNode) {
          $this->collapse($childNode, $maximumDepth);
        }
      }
    }

    /**
     * @param \DOMNode $node
     * @param int $maximumDepth
     */
    private function collapseNode(\DOMNode $node, int $maximumDepth): void {
      if ($node instanceof \DOMElement) {
        $this->startElementNS($node->prefix, $node->localName, $node->namespaceURI);
        $this->collapse($node->attributes, $maximumDepth - 1);
        $this->collapse($node->childNodes, $maximumDepth - 1);
        $this->endElement();
      } elseif ($node instanceof \DOMCdataSection) {
        $this->writeCdata($node->textContent);
      } elseif ($node instanceof \DOMText) {
        if (!$node->isWhitespaceInElementContent()) {
          $this->text($node->textContent);
        }
      } elseif ($node instanceof \DOMComment) {
        $this->writeComment($node->textContent);
      } elseif ($node instanceof \DOMProcessingInstruction) {
        $this->writePi($node->target, $node->textContent);
      } elseif ($node instanceof \DOMAttr) {
        $this->writeAttributeNS($node->prefix, $node->localName, $node->namespaceURI, $node->value);
      } elseif (isset($node->childNodes)) {
        $this->collapse($node->childNodes, $maximumDepth);
      }
    }
  }
}
