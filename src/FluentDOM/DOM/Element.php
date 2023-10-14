<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\DOM {

  use FluentDOM\Appendable;
  use FluentDOM\Exceptions\ReadOnlyPropertyError;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Exceptions\UndeclaredPropertyError;
  use FluentDOM\Utility\Iterators\ElementIterator;
  use FluentDOM\Utility\QualifiedName;

  /**
   * FluentDOM\DOM\Element extends PHPs DOMDocument class. It adds some generic namespace handling on
   * the document level and registers extended Node classes for convenience.
   *
   * @property-read Document $ownerDocument
   * @property-read Element $nextElementSibling
   * @property-read Element $previousElementSibling
   * @property-read Element $firstElementChild
   * @property-read Element $lastElementChild
   * @property-read \DOMNode|Node\ChildNode|Node\NonDocumentTypeChildNode|Node $firstChild
   * @property-read \DOMNode|Node\ChildNode|Node\NonDocumentTypeChildNode|Node $lastChild
   */
  class Element
    extends \DOMElement
    implements
      \ArrayAccess,
      \Countable,
      \IteratorAggregate,
      Node\NonDocumentTypeChildNode,
      Node\ParentNode {

    use
      Node\ChildNode\Implementation,
      Node\QuerySelector\Implementation,
      Node\StringCast,
      Node\Xpath,
      Node\ParentNode\Implementation
      {
        Node\ParentNode\Implementation::append as appendToParentNode;
      }

    private const NAMESPACE_XMLNS = 'http://www.w3.org/2000/xmlns/';

    /**
     * Validate if an attribute exists
     *
     * @param string $qualifiedName
     * @return bool
     * @throws \LogicException
     */
    public function hasAttribute(string $qualifiedName): bool {
      [$namespaceURI, $localName] = $this->resolveTagName($qualifiedName);
      if ($namespaceURI !== '') {
        return parent::hasAttributeNS($namespaceURI, $localName);
      }
      return parent::hasAttribute($qualifiedName);
    }

    /**
     * Get an attribute value
     *
     * @param string $qualifiedName
     * @return string
     * @throws \LogicException
     */
    public function getAttribute(string $qualifiedName): string {
      [$namespaceURI, $localName] = $this->resolveTagName($qualifiedName);
      if ($namespaceURI !== '') {
        return $this->getAttributeNS($namespaceURI, $localName);
      }
      return parent::getAttribute($qualifiedName);
    }

    /**
     * Get an attribute value
     *
     * @throws \LogicException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function getAttributeNode(string $qualifiedName): ?Attribute {
      [$namespaceURI, $localName] = $this->resolveTagName($qualifiedName);
      if ($namespaceURI !== '') {
        return parent::getAttributeNodeNS($namespaceURI, $localName);
      }
      return parent::getAttributeNode($qualifiedName);
    }

    /**
     * Set an attribute on an element
     *
     * @throws \LogicException
     */
    public function setAttribute(string $qualifiedName, string $value): void {
      [$namespaceURI] = $this->resolveTagName($qualifiedName);
      if ($namespaceURI !== '') {
        parent::setAttributeNS($namespaceURI, $qualifiedName, $value);
      }
      parent::setAttribute($qualifiedName, $value);
    }

    /**
     * Set an attribute on an element
     *
     * @param string $qualifiedName
     * @return bool
     * @throws \LogicException
     */
    public function removeAttribute(string $qualifiedName): bool {
      [$namespaceURI, $localName] = $this->resolveTagName($qualifiedName);
      if ($namespaceURI !== '') {
        return $this->removeAttributeNS($namespaceURI, $localName);
      }
      return parent::removeAttribute($qualifiedName);
    }

    #[\ReturnTypeWillChange]
    public function removeAttributeNS(?string $namespace, string $localName): bool {
      if ($namespace === self::NAMESPACE_XMLNS) {
        if (parent::removeAttributeNS($namespace, $localName)) {
          // @codeCoverageIgnoreStart
          // will only be triggered if PHP is fixed
          return TRUE;
          // @codeCoverageIgnoreEnd
        }
        $namespaceDefinitionValue = $this->getAttributeNS($namespace, $localName);
        if ($this->evaluate('count(@*[namespace-uri() = '.Xpath::quote($namespaceDefinitionValue).']) = 0')) {
          return (bool)parent::removeAttributeNS($namespaceDefinitionValue, $localName);
        }
        return FALSE;
      }
      if (
        ($this->getAttribute('xmlns:'.$localName) === $namespace) &&
        ($attribute = $this->getAttributeNodeNS($namespace, $localName))
      ) {
        return (bool)parent::removeAttributeNode($attribute);
      }
      return (bool)parent::removeAttributeNS($namespace, $localName);
    }

    /**
     * Set an attribute on an element
     *
     * @param string $qualifiedName
     * @param bool $isId
     * @throws \LogicException
     */
    public function setIdAttribute(string $qualifiedName, bool $isId): void {
      [$namespaceURI, $localName] = $this->resolveTagName($qualifiedName);
      if ($namespaceURI !== '') {
        $this->setIdAttributeNS($namespaceURI, $localName, $isId);
      } else {
        parent::setIdAttribute($qualifiedName, $isId);
      }
    }

    /**
     * Append a value to the element node
     *
     * The value can be:
     *
     * - a node (automatically imported and cloned)
     * - an object implementing FluentDOM\Appendable (method appendTo())
     * - a scalar or object castable to string (adds a text node)
     * - an array (sets attributes)
     *
     * @param \Traversable|\DOMNode|Appendable|array|string|callable $nodes
     * @return void
     * @throws \LogicException|UnattachedNode
     */
    public function append(...$nodes): void {
      $document = $this->ownerDocument;
      if (!$document instanceof Document) {
        throw new \LogicException(
          sprintf('Node is not attached to a %s.', Document::class)
        );
      }
      foreach ($nodes as $node) {
        if ($node instanceof \DOMAttr) {
          $this->setAttributeNode(
            $node->ownerDocument === $document
              ? $node : $document->importNode($node)
          );
        } elseif ($node instanceof Appendable) {
          $document->namespaces()->store();
          $node->appendTo($this);
          $document->namespaces()->restore();
        } elseif ((!$node instanceof \DOMNode) && $node instanceof \Closure) {
          $this->append($node());
        } elseif (\is_array($node)) {
          $nodes = [];
          foreach ($node as $name => $data) {
            if (QualifiedName::validate((string)$name)) {
              $this->setAttribute($name, (string)$data);
            } else {
              $nodes[] = $data;
            }
          }
          $this->appendToParentNode($nodes);
        } else {
          $this->appendToParentNode($node);
        }
      }
    }

    /**
     * Append an child element
     *
     * @throws \LogicException|\DOMException
     */
    public function appendElement(
      string $qualifiedName, string|array $content = '', array $attributes = NULL
    ): Element {
      $this->appendChild(
        $node = $this->getDocument()->createElement($qualifiedName, $content, $attributes)
      );
      return $node;
    }

    /**
     * Append an xml fragment to the element node
     *
     * @param string $xmlFragment
     * @throws \InvalidArgumentException
     */
    public function appendXml(string $xmlFragment): void {
      $fragment = $this->getDocument()->createDocumentFragment();
      $fragment->appendXml($xmlFragment);
      $this->appendChild($fragment);
    }

    /**
     * save the element node as XML
     *
     * @return string
     */
    public function saveXml(): string {
      return $this->getDocument()->saveXML($this);
    }

    /**
     * Save the child nodes of this element as an XML fragment.
     *
     * @return string
     */
    public function saveXmlFragment(): string {
      $result = '';
      foreach ($this->childNodes as $child) {
        $result .= $this->getDocument()->saveXML($child);
      }
      return $result;
    }

    /**
     * save the element node as HTML
     *
     * @return string
     */
    public function saveHtml(): string {
      return $this->getDocument()->saveHTML($this);
    }

    /**
     * Allow getElementsByTagName to use the defined namespaces.
     *
     * @param string $name
     * @return \DOMNodeList
     * @throws \LogicException
     */
    public function getElementsByTagName(string $name): \DOMNodeList {
      [$namespaceURI, $localName] = $this->resolveTagName($name);
      if ($namespaceURI !== '') {
        return parent::getElementsByTagNameNS($namespaceURI, $localName);
      }
      return parent::getElementsByTagName($localName);
    }

    /**
     * The 'Clark Notation' for the element.
     *
     * @see http://www.jclark.com/xml/xmlns.htm
     */
    public function clarkNotation() : string {
      if (!$this->namespaceURI) {
        return $this->localName;
      }

      return sprintf('{%s}%s', $this->namespaceURI, $this->localName);
    }

    /***************************
     * Array Access Interface
     ***************************/

    /**
     * Validate if an offset exists. If a integer is provided
     * it will check for a child node, if a string is provided for an attribute.
     *
     * @param int|string $offset
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function offsetExists($offset): bool {
      if ($this->isNodeOffset($offset)) {
        return $this->count() > $offset;
      }
      return $this->hasAttribute($offset);
    }

    /**
     * Get a child node by its numeric index, or an attribute by its name.
     *
     * @param int|string $offset
     * @return \DOMNode|mixed|string
     * @throws \InvalidArgumentException
     */
    public function offsetGet(mixed $offset): mixed {
      if ($this->isNodeOffset($offset)) {
        return $this->childNodes->item((int)$offset);
      }
      return $this->getAttribute($offset);
    }

    /**
     * @param int|string $offset
     * @param \DOMNode|string $value
     * @throws \LogicException
     */
    public function offsetSet($offset, $value): void {
      if (NULL === $offset || $this->isNodeOffset($offset)) {
        if (!($value instanceOf \DOMNode)) {
          throw new \InvalidArgumentException(
            '$value is not a valid \\DOMNode'
          );
        }
        if (NULL === $offset) {
          $this->appendChild($value);
        } else {
          $this->replaceChild(
            $value, $this->childNodes->item((int)$offset)
          );
        }
      } else {
        $this->setAttribute($offset, (string)$value);
      }
    }

    /**
     * Remove a child node using its index or an attribute node using its name.
     *
     * @param int|string $offset
     * @throws \InvalidArgumentException
     */
    public function offsetUnset($offset): void {
      if ($this->isNodeOffset($offset)) {
        $this->removeChild($this->childNodes->item((int)$offset));
      } else {
        $this->removeAttribute($offset);
      }
    }

    /**
     * Node offsets are integers, or strings containing only digits.
     *
     * @param mixed $offset
     * @throws \InvalidArgumentException
     * @return bool
     */
    private function isNodeOffset(mixed $offset): bool {
      if (\is_int($offset) || \ctype_digit((string)$offset)) {
        return TRUE;
      }
      if ($this->isAttributeOffset($offset)) {
        return FALSE;
      }
      throw new \InvalidArgumentException(
        'Invalid offset. Use integer for child nodes and strings for attributes.'
      );
    }

    /**
     * Attribute offsets are strings that can not only contains digits.
     */
    private function isAttributeOffset(mixed $offset): bool {
      return (is_string($offset) && !ctype_digit((string)$offset));
    }

    /*************************
     * Iterator
     ************************/

    /**
     * Return Iterator for child nodes.
     *
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      return new ElementIterator($this);
    }

    /*************************
     * Countable
     ************************/

    /**
     * Return child node count
     *
     * @return int
     */
    public function count(): int {
      $nodes = $this->childNodes;
      return ($nodes instanceOf \DOMNodeList) ? $nodes->length : 0;
    }

    /**
     * Resolves a provided tag name into namespace and local name
     *
     * @param string $name
     * @return string[]
     * @throws \LogicException
     */
    private function resolveTagName(string $name): array {
      [$prefix, $localName] = QualifiedName::split($name);
      if (empty($prefix)) {
        return ['', (string)$localName];
      }
      $namespaceURI = $this->getDocument()->namespaces()->resolveNamespace($prefix);
      return [(string)$namespaceURI, (string)$localName];
    }

    /**
     * A getter for the owner document
     *
     * @return Document
     */
    private function getDocument(): Document {
      return $this->ownerDocument;
    }

    /**
     * Sets all namespaces registered on the document as xmlns attributes on the element.
     *
     * @throws \LogicException
     */
    public function applyNamespaces(string|array $prefixes = NULL): void {
      if ($prefixes !== NULL && !\is_array($prefixes)) {
        $prefixes = [$prefixes];
      }
      foreach ($this->getDocument()->namespaces() as $prefix => $namespaceURI) {
        if (
          !$this->isCurrentNamespace($prefix, $namespaceURI) &&
          ($prefixes === NULL || \in_array($prefix, $prefixes, TRUE))
        ) {
          $this->setAttribute(
            ($prefix === '#default') ? 'xmlns' : 'xmlns:'.$prefix,
            $namespaceURI
          );
        }
      }
    }

    /**
     * Return TRUE if the provided namespace is the same as the one on the element
     *
     * @param string $prefix
     * @param string $namespaceURI
     * @return bool
     */
    private function isCurrentNamespace(string $prefix, string $namespaceURI): bool {
      return (
        $namespaceURI === $this->namespaceURI &&
        $prefix === ($this->prefix ?: '#default')
      );
    }
  }
}
