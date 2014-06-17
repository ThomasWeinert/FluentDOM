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
   * @property Document $ownerElement
   */
  class Element
    extends \DOMElement
    implements \ArrayAccess, \Countable, \IteratorAggregate  {

    use Node\StringCast;

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
     * Validate if an attribute exists
     *
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name) {
      list($namespace, $localName) = $this->resolveTagName($name);
      if ($namespace != '') {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return parent::hasAttributeNS($namespace, $localName);
      } else {
        return parent::hasAttribute($name);
      }
    }

    /**
     * Call an object to append itself to the element.
     *
     * @param Appendable $object
     * @return NULL|Element
     */
    public function append(Appendable $object) {
      return $object->appendTo($this);
    }

    /**
     * Append an child element
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return \DOMElement
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
     * Evaluate an xpath expression in the context of this
     * element.
     *
     * @param string $expression
     * @param \DOMNode $context
     * @return mixed
     */
    public function evaluate($expression, \DOMNode $context = NULL) {
      return $this->getDocument()->xpath()->evaluate(
        $expression, isset($context) ? $context : $this
      );
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
     * @return \Iterator
     */
    public function getIterator() {
      return new Element\Iterator($this);
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
      return $this->hasChildNodes() ? $this->childNodes->length : 0;
    }

    /**
     * Resolves a provided tag name into namespace and local name
     *
     * @param string $name
     * @return array:string
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
  }
}