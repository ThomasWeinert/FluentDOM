<?php
/**
 * FluentDOM\Element extends PHPs DOMDocument class. It adds some generic namespace handling on
 * the document level and registers extended Node classes for convenience.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\Element extends PHPs DOMDocument class. It adds some generic namespace handling on
   * the document level and registers extended Node classes for convenience.
   *
   * @property Document $ownerDocument
   */
  class Element
    extends \DOMElement
    implements \ArrayAccess, \Countable, \IteratorAggregate  {

    use Node\StringCast;
    use Node\Xpath;

    /**
     * Validate if an attribute exists
     *
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name) {
      list($namespace, $localName) = $this->resolveTagName($name);
      if ($namespace != '') {
        return parent::hasAttributeNS($namespace, $localName);
      } else {
        return parent::hasAttribute($name);
      }
    }

    /**
     * Get an attribute value
     *
     * @param string $name
     * @return string
     */
    public function getAttribute($name) {
      list($namespace, $localName) = $this->resolveTagName($name);
      if ($namespace != '') {
        return parent::getAttributeNS($namespace, $localName);
      } else {
        return parent::getAttribute($name);
      }
    }

    /**
     * Get an attribute value
     *
     * @param string $name
     * @return Attribute|\DOMAttr
     */
    public function getAttributeNode($name) {
      list($namespace, $localName) = $this->resolveTagName($name);
      if ($namespace != '') {
        return parent::getAttributeNodeNS($namespace, $localName);
      } else {
        return parent::getAttributeNode($name);
      }
    }

    /**
     * Set an attribute on an element
     *
     * @param string $name
     * @param string $value
     * @return \DOMAttr
     */
    public function setAttribute($name, $value) {
      list($namespace) = $this->resolveTagName($name);
      if ($namespace != '') {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return parent::setAttributeNS($namespace, $name, $value);
      } else {
        return parent::setAttribute($name, $value);
      }
    }

    /**
     * Set an attribute on an element
     *
     * @param string $name
     * @return bool
     */
    public function removeAttribute($name) {
      list($namespace, $localName) = $this->resolveTagName($name);
      if ($namespace != '') {
        return parent::removeAttributeNS($namespace, $localName);
      } else {
        return parent::removeAttribute($name);
      }
    }

    /**
     * Set an attribute on an element
     *
     * @param string $name
     * @param bool $isId
     */
    public function setIdAttribute($name, $isId) {
      list($namespace, $localName) = $this->resolveTagName($name);
      if ($namespace != '') {
        parent::setIdAttributeNS($namespace, $localName, $isId);
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
     * @param mixed $value
     * @return $this|Element new element or self
     */
    public function append($value) {
      $result = NULL;
      if ($value instanceof \DOMNode) {
        $result = $this->appendNode($value);
      } elseif ($value instanceof Appendable) {
        $namespaces = $this->ownerDocument->namespaces();
        $result = $value->appendTo($this);
        $this->ownerDocument->namespaces($namespaces);
      } elseif ($value instanceof \Traversable) {
        foreach ($value as $node) {
          $this->append($node);
        }
      } elseif (is_callable($value)) {
        $result = $this->append($value());
      } elseif (is_array($value)) {
        foreach ($value as $name => $data) {
          if (is_scalar($data) && QualifiedName::validate($name)) {
            $this->setAttribute($name, (string)$data);
          } elseif (!is_scalar($data)) {
            $this->append($data);
          }
        }
      } elseif (is_scalar($value) || method_exists($value, '__toString')) {
        $result = $this->appendChild(
          $this->ownerDocument->createTextNode((string)$value)
        );
      }
      return ($result instanceof Element) ? $result : $this;
    }

    /**
     * @param \DOMNode $node
     * @return \DOMAttr|\DOMNode
     */
    private function appendNode(\DOMNode $node) {
      if ($node instanceof \DOMDocument) {
        if ($node->documentElement instanceof \DOMElement) {
          return $this->appendChild(
            $this->getDocument()->importNode($node->documentElement)
          );
        }
        return NULL;
      } elseif ($node->ownerDocument !== $this->getDocument()) {
        $node = $this->getDocument()->importNode($node, TRUE);
      } elseif ($node->parentNode instanceOf \DOMNode) {
        $node = $node->cloneNode(TRUE);
      }
      if ($node instanceof \DOMAttr) {
        return $this->setAttributeNode($node);
      }
      return $this->appendChild($node);
    }

    /**
     * Append an child element
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return Element
     */
    public function appendElement($name, $content = '', array $attributes = NULL) {
      $this->appendChild(
        $node = $this->getDocument()->createElement($name, $content, $attributes)
      );
      return $node;
    }

    /**
     * Append an xml fragment to the element node
     *
     * @param string $xmlFragment
     */
    public function appendXml($xmlFragment) {
      $fragment = $this->getDocument()->createDocumentFragment();
      $fragment->appendXML($xmlFragment);
      $this->appendChild($fragment);
    }

    /**
     * save the element node as XML
     *
     * @return string
     */
    public function saveXml() {
      return $this->getDocument()->saveXML($this);
    }

    /**
     * Save the child nodes of this element as an XML fragment.
     *
     * @return string
     */
    public function saveXmlFragment() {
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
    public function saveHtml() {
      return $this->getDocument()->saveHTML($this);
    }

    /**
     * Put the current node into a FluentDOM\Query
     * and call find() on it.
     *
     * @param string $expression
     * @return Query
     */
    public function find($expression) {
      return \FluentDOM::Query($this)->find($expression);
    }

    /**
     * Allow getElementsByTagName to use the defined namespaces.
     *
     * @param string $name
     * @return \DOMNodeList
     */
    public function getElementsByTagName($name) {
      list($namespace, $localName) = $this->resolveTagName($name);
      if ($namespace != '') {
        return parent::getElementsByTagNameNS($namespace, $localName);
      } else {
        return parent::getElementsByTagName($localName);
      }
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
    public function offsetExists($offset) {
      if ($this->isNodeOffset($offset)) {
        return $this->count() > $offset;
      } else {
        return $this->hasAttribute($offset);
      }
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
      } else {
        return $this->getAttribute($offset);
      }
    }

    /**
     * @param int|string $offset
     * @param \DOMNode|string $value
     * @return \DOMAttr|\DOMNode|void
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
          return $this->appendChild($value);
        } else {
          return $this->replaceChild(
            $value, $this->childNodes->item((int)$offset)
          );
        }
      } else {
        return $this->setAttribute($offset, (string)$value);
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
    private function isNodeOffset($offset) {
      if (is_int($offset) || ctype_digit((string)$offset)) {
        return TRUE;
      } elseif ($this->isAttributeOffset($offset)) {
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
    private function isAttributeOffset($offset) {
      return (is_string($offset) && !ctype_digit((string)$offset));
    }

    /*************************
     * Iterator
     ************************/

    /**
     * Return Iterator for child nodes.
     *
     * @return Iterators\ElementIterator
     */
    public function getIterator() {
      return new Iterators\ElementIterator($this);
    }

    /*************************
     * Countable
     ************************/

    /**
     * Return child node count
     *
     * @return int
     */
    public function count() {
      $nodes = $this->childNodes;
      return ($nodes instanceOf \DOMNodeList) ? $nodes->length : 0;
    }

    /**
     * Resolves a provided tag name into namespace and local name
     *
     * @param string $name
     * @return string[]
     */
    private function resolveTagName($name) {
      $namespace = '';
      list($prefix, $localName) = QualifiedName::split($name);
      if ($prefix) {
        $namespace = $this->getDocument()->getNamespace($prefix);
        return array($namespace, $localName);
      }
      return array($namespace, $localName);
    }

    /**
     * A getter for the owner document
     *
     * @return Document
     */
    private function getDocument() {
      return $this->ownerDocument;
    }

    /**
     * Sets all namespaces registered on the document as xmlns attributes on the element.
     *
     * @param NULL|string|array $prefixes
     */
    public function applyNamespaces($prefixes = NULL) {
      if ($prefixes !== NULL && !is_array($prefixes)) {
        $prefixes = array($prefixes);
      }
      foreach ($this->getDocument()->namespaces() as $prefix => $namespace) {
        if (
          !$this->isCurrentNamespace($prefix, $namespace) &&
          ($prefixes === NULL || in_array($prefix, $prefixes))
        ) {
          $this->setAttribute(
            ($prefix == '#default') ? 'xmlns' : 'xmlns:'.$prefix,
            $namespace
          );
        }
      }
    }

    /**
     * Return true if the provided namespace is the same as the one on the element
     *
     * @param string $prefix
     * @param string $namespace
     * @return bool
     */
    private function isCurrentNamespace($prefix, $namespace) {
      return (
        $namespace == $this->namespaceURI &&
        $prefix == ($this->prefix ?: '#default')
      );
    }
  }
}