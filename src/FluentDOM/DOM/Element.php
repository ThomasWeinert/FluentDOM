<?php
/**
 * FluentDOM\DOM\Element extends PHPs DOMDocument class. It adds some generic namespace handling on
 * the document level and registers extended Node classes for convenience.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\DOM {

  use FluentDOM\Appendable;
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
      Node,
      Node\ChildNode,
      Node\NonDocumentTypeChildNode,
      Node\ParentNode {

    use
      Node\ChildNode\Implementation,
      Node\NonDocumentTypeChildNode\Implementation,
      Node\QuerySelector\Implementation,
      Node\StringCast,
      Node\Xpath,
      Node\ParentNode\Implementation
      {
        Node\ParentNode\Implementation::append as appendToParentNode;
      }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name) {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling() !== NULL;
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling() !== NULL;
      case 'firstElementChild' :
        return $this->getFirstElementChild() !== NULL;
      case 'lastElementChild' :
        return $this->getLastElementChild() !== NULL;
      }
      return FALSE;
    }

    /**
     * @param string $name
     * @return \DOMNode|Element|NULL
     */
    public function __get(string $name) {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling();
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling();
      case 'firstElementChild' :
        return $this->getFirstElementChild();
      case 'lastElementChild' :
        return $this->getLastElementChild();
      }
      return $this->$name;
    }

    /**
     * @param string $name
     * @param $value
     * @throws \BadMethodCallException
     */
    public function __set(string $name, $value) {
      $this->blockReadOnlyProperties($name);
      $this->$name = $value;
    }

    /**
     * @param string $name
     * @throws \BadMethodCallException
     */
    public function __unset(string $name) {
      $this->blockReadOnlyProperties($name);
      unset($this->$name);
    }

    /**
     * @param string $name
     * @throws \BadMethodCallException
     */
    private function blockReadOnlyProperties(string $name) {
      switch ($name) {
      case 'nextElementSibling' :
      case 'previousElementSibling' :
      case 'firstElementChild' :
      case 'lastElementChild' :
        throw new \BadMethodCallException(
          sprintf(
            'Can not write readonly property %s::$%s.',
            get_class($this), $name
          )
        );
      }
    }

    /**
     * Validate if an attribute exists
     *
     * @param string $name
     * @return bool
     * @throws \LogicException
     */
    public function hasAttribute($name): bool {
      list($namespaceURI, $localName) = $this->resolveTagName($name);
      if ($namespaceURI !== '') {
        return parent::hasAttributeNS($namespaceURI, $localName);
      }
      return parent::hasAttribute($name);
    }

    /**
     * Get an attribute value
     *
     * @param string $name
     * @return string|NULL
     * @throws \LogicException
     */
    public function getAttribute($name) {
      list($namespaceURI, $localName) = $this->resolveTagName($name);
      if ($namespaceURI !== '') {
        return parent::getAttributeNS($namespaceURI, $localName);
      }
      return parent::getAttribute($name);
    }

    /**
     * Get an attribute value
     *
     * @param string $name
     * @return Attribute|\DOMAttr|NULL
     * @throws \LogicException
     */
    public function getAttributeNode($name) {
      list($namespaceURI, $localName) = $this->resolveTagName($name);
      if ($namespaceURI !== '') {
        return parent::getAttributeNodeNS($namespaceURI, $localName);
      }
      return parent::getAttributeNode($name);
    }

    /**
     * Set an attribute on an element
     *
     * @todo validate return value
     * @param string $name
     * @param string $value
     * @return Attribute
     * @throws \LogicException
     */
    public function setAttribute($name, $value) {
      list($namespaceURI) = $this->resolveTagName($name);
      if ($namespaceURI !== '') {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return parent::setAttributeNS($namespaceURI, $name, $value);
      }
      return parent::setAttribute($name, $value);
    }

    /**
     * Set an attribute on an element
     *
     * @param string $name
     * @return bool
     * @throws \LogicException
     */
    public function removeAttribute($name): bool {
      list($namespaceURI, $localName) = $this->resolveTagName($name);
      if ($namespaceURI !== '') {
        return (bool)parent::removeAttributeNS($namespaceURI, $localName);
      }
      return (bool)parent::removeAttribute($name);
    }

    /**
     * Set an attribute on an element
     *
     * @param string $name
     * @param bool $isId
     * @throws \LogicException
     */
    public function setIdAttribute($name, $isId) {
      list($namespaceURI, $localName) = $this->resolveTagName($name);
      if ($namespaceURI !== '') {
        parent::setIdAttributeNS($namespaceURI, $localName, $isId);
      } else {
        parent::setIdAttribute($name, $isId);
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
     * @param \Traversable|\DOMNode|Appendable|array|string|callable $value
     * @return $this|Element
     * @throws \LogicException
     */
    public function append($value): Element {
      if ($value instanceof \DOMAttr) {
        $this->setAttributeNode(
          $value->ownerDocument === $this->ownerDocument
            ? $value : $this->ownerDocument->importNode($value)
        );
      } elseif ($value instanceof Appendable) {
        $this->ownerDocument->namespaces()->store();
        $value->appendTo($this);
        $this->ownerDocument->namespaces()->restore();
      } elseif ($value instanceof \Closure && !$value instanceof \DOMNode) {
        $this->append($value());
      } elseif (is_array($value)) {
        $nodes = [];
        foreach ($value as $name => $data) {
          if (QualifiedName::validate($name)) {
            $this->setAttribute($name, (string)$data);
          } else {
            $nodes[] = $data;
          }
        }
        $this->appendToParentNode($nodes);
      } else {
        $this->appendToParentNode($value);
      }
      return $this;
    }

    /**
     * Append an child element
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return Element
     * @throws \LogicException
     */
    public function appendElement(string $name, $content = '', array $attributes = NULL): Element {
      $this->appendChild(
        $node = $this->getDocument()->createElement($name, $content, $attributes)
      );
      return $node;
    }

    /**
     * Append an xml fragment to the element node
     *
     * @param string $xmlFragment
     * @throws \InvalidArgumentException
     */
    public function appendXml(string $xmlFragment) {
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
    public function getElementsByTagName($name): \DOMNodeList {
      list($namespaceURI, $localName) = $this->resolveTagName($name);
      if ($namespaceURI !== '') {
        return parent::getElementsByTagNameNS($namespaceURI, $localName);
      }
      return parent::getElementsByTagName($localName);
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
    public function offsetGet($offset) {
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
    public function offsetSet($offset, $value) {
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
    public function offsetUnset($offset) {
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
    private function isNodeOffset($offset): bool {
      if (is_int($offset) || ctype_digit((string)$offset)) {
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
     *
     * @param mixed $offset
     * @return bool
     */
    private function isAttributeOffset($offset): bool {
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
      list($prefix, $localName) = QualifiedName::split($name);
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
     * @param NULL|string|array $prefixes
     * @throws \LogicException
     */
    public function applyNamespaces($prefixes = NULL) {
      if ($prefixes !== NULL && !is_array($prefixes)) {
        $prefixes = [$prefixes];
      }
      foreach ($this->getDocument()->namespaces() as $prefix => $namespaceURI) {
        if (
          !$this->isCurrentNamespace($prefix, $namespaceURI) &&
          ($prefixes === NULL || in_array($prefix, $prefixes, TRUE))
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