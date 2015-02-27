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
   * @property-read Document $ownerDocument
   * @property-read Element $nextElementSibling
   * @property-read Element $previousElementSibling
   * @property-read Element $firstElementChild
   * @property-read Element $lastElementChild
   * @property-read \DOMNode|Node\ChildNode|Node\NonDocumentTypeChildNode $firstChild
   * @property-read \DOMNode|Node\ChildNode|Node\NonDocumentTypeChildNode $lastChild
   */
  class Element
    extends \DOMElement
    implements
      \ArrayAccess,
      \Countable,
      \IteratorAggregate,
      Node\ChildNode,
      Node\NonDocumentTypeChildNode,
      Node\ParentNode {

    use
      Node\ChildNode\Implementation,
      Node\NonDocumentTypeChildNode\Implementation,
      Node\ParentNode\Implementation,
      Node\QuerySelector\Implementation,
      Node\StringCast,
      Node\Xpath,
      HHVM\Properties {
        Node\ParentNode\Implementation::append as appendToParentNode;
      }

    public function __get($name) {
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
      return $this->getParentProperty($name);
    }

    public function __set($name, $value) {
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
      $this->setParentProperty($name, $value);
      return TRUE;
    }

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
     * @return $this
     */
    public function append($value) {
      if ($value instanceof \DOMAttr) {
        $this->setAttributeNode(
          $value->ownerDocument === $this->ownerDocument
            ? $value : $this->ownerDocument->importNode($value)
        );
      } elseif ($value instanceof Appendable) {
        $namespaces = $this->ownerDocument->namespaces();
        $value->appendTo($this);
        $this->ownerDocument->namespaces($namespaces);
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
      list($prefix, $localName) = QualifiedName::split($name);
      if (empty($prefix)) {
        return array('', $localName);
      } else {
        $namespace = $this->getDocument()->getNamespace($prefix);
        return array($namespace, $localName);
      }
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
            ($prefix === '#default') ? 'xmlns' : 'xmlns:'.$prefix,
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
        $namespace === $this->namespaceURI &&
        $prefix === ($this->prefix ?: '#default')
      );
    }
  }
}