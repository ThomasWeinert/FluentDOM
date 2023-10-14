<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
/** @noinspection PhpComposerExtensionStubsInspection */
declare(strict_types=1);

namespace FluentDOM {

  use FluentDOM\Utility\Namespaces;
  use FluentDOM\Utility\QualifiedName;

  class XMLWriter extends \XMLWriter {

    private Namespaces $_namespaces;

    private XMLWriter\NamespaceStack $_xmlnsStack;

    public function __construct() {
      $this->_namespaces = new Namespaces();
      $this->_xmlnsStack = new XMLWriter\NamespaceStack();
    }

    /**
     * register a namespace prefix for the xml reader, it will be used in
     * next() and other methods with a tag name argument
     *
     * @throws \LogicException
     */
    public function registerNamespace(string $prefix, string $namespaceURI): void
    {
      $this->_namespaces[$prefix] = $namespaceURI;
    }

    /**
     * Add the current namespace configuration as xmlns* attributes to the element node.
     */
    public function applyNamespaces(): void {
      foreach ($this->_namespaces as $prefix => $namespaceURI) {
        $this->writeAttribute(
          empty($prefix) || $prefix === '#default' ? 'xmlns' : 'xmlns:'.$prefix, $namespaceURI
        );
      }
    }

    public function startElement(string $name): bool {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespaceURI = $this->_namespaces->resolveNamespace((string)$prefix);
      return $this->startElementNS((string)$prefix, $localName, $namespaceURI);
    }

    public function writeElement(string $name, string $content = NULL): bool {
      list($prefix, $localName) = QualifiedName::split($name);
      $namespaceURI = $this->_namespaces->resolveNamespace((string)$prefix);
      return $this->writeElementNS((string)$prefix, $localName, $namespaceURI, $content);
    }

    public function startElementNS(?string $prefix, string $name, ?string $namespace): bool {
      $this->_xmlnsStack->push();
      if ($this->_xmlnsStack->isDefined($prefix, $namespace)) {
        $result = parent::startElement(empty($prefix) ? $name : $prefix.':'.$name);
      } else {
        $result = parent::startElementNs(empty($prefix) ? NULL : $prefix, $name, $namespace);
        $this->_xmlnsStack->add($prefix, $namespace);
      }
      return $result;
    }

    public function writeElementNS(
      ?string $prefix, string $name, ?string $namespace, string $content = NULL
    ): bool {
      if ($this->_xmlnsStack->isDefined($prefix, $namespace)) {
        return parent::writeElement(empty($prefix) ? $name : $prefix.':'.$name, $content);
      }
      return parent::writeElementNs(empty($prefix) ? NULL : $prefix, $name, $namespace, $content);
    }

    public function endElement(): bool {
      $this->_xmlnsStack->pop();
      return parent::endElement();
    }

    public function startAttribute(string $name): bool {
      list($prefix, $localName) = QualifiedName::split($name);
      return $this->startAttributeNS(
        (string)$prefix, $localName, $this->_namespaces->resolveNamespace((string)$prefix)
      );
    }

    public function writeAttribute(string $name, string $value): bool {
      list($prefix, $localName) = QualifiedName::split($name);
      return $this->writeAttributeNS(
        (string)$prefix, $localName, $this->_namespaces->resolveNamespace((string)$prefix), $value
      );
    }

    public function startAttributeNS(?string $prefix, string $name, ?string $namespace): bool {
      if (empty($prefix)) {
        return parent::startAttribute($name);
      }
      if ($this->_xmlnsStack->isDefined($prefix, $namespace)) {
        return parent::startAttribute($prefix.':'.$name);
      }
      return parent::startAttributeNs($prefix, $name, $namespace);
    }

    public function writeAttributeNS(
      ?string $prefix, string $name, ?string $namespace, string $value
    ): bool {
      if ((empty($prefix) && $name === 'xmlns') || $prefix === 'xmlns') {
        $namespacePrefix = empty($prefix) ? '' : $name;
        $namespaceURI = $value;
        if (!$this->_xmlnsStack->isDefined($namespacePrefix, $namespaceURI)) {
          $result = parent::writeAttribute(empty($prefix) ? 'xmlns' : 'xmlns:'.$name, $namespaceURI);
          $this->_xmlnsStack->add($namespacePrefix, $namespaceURI);
          return $result;
        }
        return FALSE;
      }
      if (empty($prefix)) {
        return parent::writeAttribute($name, $value);
      }
      if ($this->_xmlnsStack->isDefined($prefix, $namespace)) {
        return parent::writeAttribute($prefix.':'.$name, $value);
      }
      return parent::writeAttributeNs($prefix, $name, $namespace, $value);
    }

    /**
     * Write a DOM node
     */
    public function collapse(\DOMNode|iterable $nodes, int $maximumDepth = 1000): void {
      if ($maximumDepth <= 0) {
        return;
      }
      if ($nodes instanceof \DOMNode) {
        $this->collapseNode($nodes, $maximumDepth);
      } else {
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
