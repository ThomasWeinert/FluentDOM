<?php
/**
* FluentDOMCss is used for the FluentDOM:css property, providing an array like interface
* to the css properties in the style attribute of the selected nodes(s)
*
* It acts like the FluentDOMStyle::css() method. If you read css properties it uses the first
* selected node. Write actions are applied to all matches element nodes.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2010 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
*/

/**
* FluentDOMCss is used for the FluentDOM:css property, providing an array like interface
* to the css properties in the style attribute of the selected nodes(s)
*
* It acts like the FluentDOMStyle::css() method. If you read css properties it uses the first
* selected node. Write actions are applied to all matches element nodes.
*
* @package FluentDOM
*/
class FluentDOMCss implements ArrayAccess, Countable, IteratorAggregate {

  /**
  * Pattern to decode the style property string
  */
  const STYLE_PATTERN = '((?:^|;)\s*(?P<name>[-\w]+)\s*:\s*(?P<value>[^;]+))';

  /**
  * owner object
  * @var FluentDOM
  */
  private $_fd = NULL;


  /**
  * Store the FluentDOM instance for later use and decode the style string into an array
  *
  * @param FluentDOM $fd
  */
  public function __construct(FluentDOMStyle $fd) {
    $this->_fd = $fd;
  }


  /**
  * Allow to use isset() and array syntax to check if a css property is set on
  * the first matched node.
  *
  * @see ArrayAccess::offsetExists()
  */
  public function offsetExists($name) {
    if (isset($this->_fd[0]) &&
        $this->_fd[0] instanceof DOMElement) {
      $properties = new FluentDOMCssProperties($this->_fd[0]->getAttribute('style'));
      return isset($properties[$name]);
    }
    return FALSE;
  }

  /**
  * Allow to use array syntax to read a css property value from first matched node.
  *
  * @see ArrayAccess::offsetGet()
  * @param string $name
  * @return $value
  */
  public function offsetGet($name) {
    if (isset($this->_fd[0]) &&
        $this->_fd[0] instanceof DOMElement) {
      $properties = new FluentDOMCssProperties($this->_fd[0]->getAttribute('style'));
      return $properties[$name];
    }
    return FALSE;
  }

  /**
  * Allow to use array syntax to change a css property value on all matched nodes.
  *
  * @see ArrayAccess::offsetSet()
  * @param string $name
  * @param string $value
  */
  public function offsetSet($name, $value) {
    $this->_fd->css($name, $value);
  }

  /**
  * Allow to use unset and array syntax to remove a css property value on
  * all matched nodes.
  *
  * @see ArrayAccess::offsetUnset()
  * @param string $name
  */
  public function offsetUnset($name) {
    foreach ($this->_fd as $node) {
      if ($node instanceof DOMElement &&
          $node->hasAttribute('style')) {
        $properties = new self(NULL, $node->getAttribute('style'));
        unset($properties[$name]);
        if (count($properties) > 0) {
          $node->setAttribute('style', (string)$properties);
        } else {
          $node->removeAttribute('style');
        }
      }
    }
  }

  /**
  * Get an iterator for the properties
  *
  * @see IteratorAggregate::getIterator()
  * @return ArrayIterator
  */
  public function getIterator() {
    if (isset($this->_fd[0]) &&
        $this->_fd[0] instanceof DOMElement) {
      $properties = new FluentDOMCssProperties($this->_fd[0]->getAttribute('style'));
      return $properties->getIterator();
    }
    return new ArrayIterator(array());
  }

  /**
  * Get the property count of the first selected node
  *
  * @see Countable::count()
  * @return integer
  */
  public function count() {
    if (isset($this->_fd[0]) &&
        $this->_fd[0] instanceof DOMElement) {
      $properties = new FluentDOMCssProperties($this->_fd[0]->getAttribute('style'));
      return count($properties);
    }
    return 0;
  }
}