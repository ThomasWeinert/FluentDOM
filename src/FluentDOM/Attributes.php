<?php
/**
* FluentDOMAttributes is used for the FluentDOM:attr property, providing an array like interface
* to the attributes of the selected nodes(s)
*
* It acts like the FluentDOM::attr() method. If you read attributes it uses the first
* selected node. Write actions are applied to all matches element nodes.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2010 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
*/

/*
* FluentDOMAttributes is used for the FluentDOM:attr property, providing an array like interface
* to the attributes of the selected nodes(s)
*
* It acts like the FluentDOM::attr() method. If you read attributes it uses the first
* selected node. Write actions are applied to all matches element nodes.
*
* @package FluentDOM
*/
class FluentDOMAttributes implements ArrayAccess, Countable, IteratorAggregate {

  /**
  * owner object
  * @var FluentDOM
  */
  private $_fd = NULL;

  /**
  * Store the FluentDOM instance for later use
  *
  * @param FluentDOM $fd
  */
  public function __construct(FluentDOM $fd) {
    $this->_fd = $fd;
  }

  /**
  * Convert the attributes of the first node into an array
  *
  * @return array
  */
  public function toArray() {
    $result = array();
    if (isset($this->_fd[0]) &&
        $this->_fd[0] instanceof DOMElement) {
      foreach ($this->_fd[0]->attributes as $attribute) {
        $result[$attribute->name] = $attribute->value;
      }
    }
    return $result;
  }

  /**
  * Check if the first selected node has the specified attribute
  *
  * @see ArrayAccess::offsetExists()
  * @param string $name
  * @return boolean
  */
  public function offsetExists($name) {
    if (isset($this->_fd[0]) &&
        $this->_fd[0] instanceof DOMElement) {
      return $this->_fd[0]->hasAttribute($name);
    }
    return FALSE;
  }

  /**
  * Read the specified attribute from the first node
  *
  * @see ArrayAccess::offsetGet()
  * @see FluentDOM::attr()
  * @example properties/attr-get.php Usage: Get attribute property
  * @param string $name
  * @return string
  */
  public function offsetGet($name) {
    return $this->_fd->attr($name);
  }

  /**
  * Set the attribute on all selected element nodes
  *
  * @see ArrayAccess::offsetSet()
  * @see FluentDOM::attr()
  * @example properties/attr-set.php Usage: Set attribute property
  * @param string $name
  * @param string $value
  */
  public function offsetSet($name, $value) {
    $this->_fd->attr($name, $value);
  }

  /**
  * Remove the attribute(s) on all selected element nodes
  *
  * @see ArrayAccess::offsetUnset()
  * @see FluentDOM::removeAttr()
  * @example properties/attr-unset.php Usage: Remove attribute properties
  * @param string|array $name
  */
  public function offsetUnset($name) {
    $this->_fd->removeAttr($name);
  }

  /**
  * Get an iterator for the attributes of the first node
  *
  * @see IteratorAggregate::getIterator()
  * @return ArrayIterator
  */
  public function getIterator() {
    return new ArrayIterator($this->toArray());
  }

  /**
  * Get the attribute count of the first selected node
  *
  * @see Countable::count()
  * @return integer
  */
  public function count() {
    if (isset($this->_fd[0]) &&
        $this->_fd[0] instanceof DOMElement) {
      return $this->_fd[0]->attributes->length;
    }
    return 0;
  }
}