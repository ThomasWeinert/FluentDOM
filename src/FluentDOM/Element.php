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

    /**
     * Set an attribute on an element
     *
     * @param string $name
     * @param string $value
     * @return \DOMAttr
     */
    public function setAttribute($name, $value) {
      $namespace = '';
      list($prefix, $localName) = QualifiedName::split($name);
      if ($this->ownerDocument instanceOf Document && $prefix) {
        $namespace = $this->ownerDocument->getNamespace($prefix);
      }
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
      $namespace = '';
      list($prefix, $localName) = QualifiedName::split($name);
      if ($this->ownerDocument instanceOf Document && $prefix) {
        $namespace = $this->ownerDocument->getNamespace($prefix);
      }
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
        $node = $this->ownerDocument->createElement($name, $content, $attributes)
      );
      return $node;
    }

    /**
     * Append an xml fragment to the element node
     *
     * @param string $xmlFragment
     */
    public function appendXml($xmlFragment) {
      $fragment = $this->ownerDocument->createDocumentFragment();
      $fragment->appendXML($xmlFragment);
      $this->appendChild($fragment);
    }

    /**
     * save the element node as XML
     *
     * @return string
     */
    public function saveXml() {
      return $this->ownerDocument->saveXML($this);
    }

    /**
     * Save the child nodes of this element as an XML fragment.
     *
     * @return string
     */
    public function saveXmlFragment() {
      $result = '';
      foreach ($this->childNodes as $child) {
        $result .= $this->ownerDocument->saveXML($child);
      }
      return $result;
    }

    /**
     * save the element node as HTML
     *
     * @return string
     */
    public function saveHtml() {
      return $this->ownerDocument->saveHTML($this);
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
      return $this->ownerDocument->xpath()->evaluate(
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
        return $this->hasChildNodes() && $this->childNodes->length > $offset;
      } elseif ($this->isAttributeOffset($offset)) {
        return $this->hasAttribute($offset);
      }
      throw $this->createInvalidOffsetException();
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
      } elseif ($this->isAttributeOffset($offset)) {
        return $this->getAttribute($offset);
      }
      throw $this->createInvalidOffsetException();
    }

    /**
     * @param int|string $offset
     * @param \DOMNode|string $value
     * @return \DOMAttr|\DOMNode|void
     * @throws \LogicException
     */
    public function offsetSet($offset, $value) {
      if (NULL === $offset) {
        return $this->appendChild($value);
      } elseif ($this->isNodeOffset($offset)) {
        return $this->replaceChild(
          $value, $this->childNodes->item((int)$offset)
        );
      } elseif ($this->isAttributeOffset($offset)) {
        return $this->setAttribute($offset, $value);
      }
      throw $this->createInvalidOffsetException();
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
        return;
      } elseif ($this->isAttributeOffset($offset)) {
        $this->removeAttribute($offset);
        return;
      }
      throw $this->createInvalidOffsetException();
    }

    private function isNodeOffset($offset) {
      return (is_int($offset) || ctype_digit((string)$offset));
    }

    private function isAttributeOffset($offset) {
      return (is_string($offset) && !ctype_digit((string)$offset));
    }

    private function createInvalidOffsetException() {
      return new \InvalidArgumentException(
        'Invalid offset. Use integer for child nodes and strings for attributes.'
      );
    }

    /*************************
     * Iterator
     ************************/

    public function getIterator() {
      return new Element\Iterator($this);
    }

    /*************************
     * Countable
     ************************/

    public function count() {
      return $this->hasChildNodes() ? $this->childNodes->length : 0;
    }
  }
}