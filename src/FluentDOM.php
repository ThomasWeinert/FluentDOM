<?php
/**
* FluentDOM implements a jQuery like replacement for DOMNodeList
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
*/

/**
* Include the core class
*/
require_once(dirname(__FILE__).'/FluentDOM/Core.php');
/**
* Include the handler helper class
*/
require_once(dirname(__FILE__).'/FluentDOM/Handler.php');

/**
* Include the attributes helper class
*/
require_once(dirname(__FILE__).'/FluentDOM/Attributes.php');
/**
* Include the data-attributes helper class
*/
require_once(dirname(__FILE__).'/FluentDOM/Data.php');

/**
* Function to create a new FluentDOM instance and loads data into it if
* a valid $source is provided.
*
* @param mixed $source
* @param string $contentType optional, default value 'text/xml'
* @return FluentDOM
*/
function FluentDOM($source = NULL, $contentType = 'text/xml') {
  $result = new FluentDOM();
  if (isset($source)) {
    return $result->load($source, $contentType);
  } else {
    return $result;
  }
}

/**
* FluentDOM implements a jQuery like replacement for DOMNodeList
*
* @property FluentDOMAttributes $attr Access attributes
* @method bool empty() Remove all child nodes from the set of matched elements.
* @method DOMDocument clone() Clone matched DOM Elements and select the clones.
*
* @package FluentDOM
*/
class FluentDOM extends FluentDOMCore {

  /**
  * declaring an empty() or clone() method will crash the parser so we use some magic
  *
  * @param string $name
  * @param array $arguments
  * @return mixed
  */
  public function __call($name, $arguments) {
    switch (strtolower($name)) {
    case 'empty' :
      return $this->_emptyNodes();
    case 'clone' :
      return $this->_cloneNodes();
    default :
      throw new BadMethodCallException('Unknown method '.get_class($this).'::'.$name);
    }
  }

  /**
  * Allow read access to "attr" as a dynamic property.
  * Call inherited method for other properties.
  *
  * This allows to get/set xml attributes using array syntax.
  *
  * @see FluentDOM/FluentDOMCore::__get()
  * @param string $name
  * @return mixed
  */
  public function __get($name) {
    switch ($name) {
    case 'attr' :
      return new FluentDOMAttributes($this);
    case 'data' :
      if (count($this->_array) > 0 &&
          $this->_array[0] instanceof DOMElement) {
        return new FluentDOMData($this->_array[0]);
      } else {
        throw new UnexpectedValueException(
          'UnexpectedValueException: first selected node is no element.'
        );
      }
    }
    return parent::__get($name);
  }

  /**
  * Allow write access to "attr" as a dynamic property.
  * Call inherited method for other properties.
  *
  * This allows to set tag attributes using an array.
  *
  * @see FluentDOM/FluentDOMCore::__get()
  * @param string $name
  * @param mixed $value
  */
  public function __set($name, $value) {
    switch ($name) {
    case 'attr' :
      if ($value instanceOf FluentDOMAttributes) {
        $this->attr($value->toArray());
      } else {
        $this->attr($value);
      }
      return;
    }
    parent::__set($name, $value);
  }

  /*
  * Object Accessors
  */

  /**
  * Execute a function within the context of every matched element.
  *
  * @param callback $function
  * @return FluentDOM
  */
  public function each($function) {
    if ($this->_isCallback($function, TRUE, FALSE)) {
      foreach ($this->_array as $index => $node) {
        call_user_func($function, $node, $index);
      }
    }
    return $this;
  }

  /*
  * Miscellaneous
  */

  /**
  * Retrieve the matched DOM elements in an array.
  *
  * @return array
  */
  public function toArray() {
    return $this->_array;
  }

  /*
  * Traversing - Filtering
  */

  /**
  * Reduce the set of matched elements to a single element.
  *
  * @example eq.php Usage Example: FluentDOM::eq()
  * @param integer $position Element index (start with 0)
  * @return FluentDOM
  */
  public function eq($position) {
    $result = $this->spawn();
    if ($position < 0) {
      $position = count($this->_array) + $position;
    }
    if (isset($this->_array[$position])) {
      $result->push($this->_array[$position]);
    }
    return $result;
  }

  /**
  * Removes all elements from the set of matched elements that do not match
  * the specified expression(s).
  *
  * @example filter-expr.php Usage Example: FluentDOM::filter() with XPath expression
  * @example filter-fn.php Usage Example: FluentDOM::filter() with Closure
  * @param string|callback $expr XPath expression or callback function
  * @return FluentDOM
  */
  public function filter($expr) {
    $result = $this->spawn();
    foreach ($this->_array as $index => $node) {
      $check = TRUE;
      if (is_string($expr)) {
        $check = $this->_test($expr, $node, $index);
      } elseif ($this->_isCallback($expr, TRUE, FALSE)) {
        $check = call_user_func($expr, $node, $index);
      }
      if ($check) {
        $result->push($node);
      }
    }
    return $result;
  }

  /**
  * Retrieve the matched DOM elements in an array. A negative position will be counted from the end.
  *
  * @param integer|NULL optional offset of a single element to get.
  * @return array()
  */
  public function get($position = NULL) {
    if (!isset($position)) {
      return $this->_array;
    }
    if ($position < 0) {
      $position = count($this->_array) + $position;
    }
    if (isset($this->_array[$position])) {
      return array($this->_array[$position]);
    } else {
      return array();
    }
  }

  /**
  * Search for a given element from among the matched elements.
  *
  * @param NULL|string|DOMNode|DOMNodelist|Iterator $expr
  * @return integer
  */
  public function index($expr = NULL) {
    if (count($this->_array) > 0) {
      if (is_null($expr)) {
        $counter = -1;
        $targetNode = $this->_array[0];
        $nodeList = $this->_match('preceding-sibling::node()', $targetNode);
        foreach ($nodeList as $node) {
          if ($this->_isNode($node)) {
            $counter++;
          }
        }
        return $counter + 1;
      } elseif (is_string($expr)) {
        foreach ($this->_array as $index => $node) {
          if ($this->_test($expr, $node)) {
            return $index;
          }
        }
      } else {
        $targetNode = $this->_getContentElement($expr);
        foreach ($this->_array as $index => $node) {
          if ($node->isSameNode($targetNode)) {
            return $index;
          }
        }
      }
    }
    return -1;
  }

  /**
  * Reduce the set of matched elements to those that have
  * a descendant that matches the selector or DOM element.
  *
  * @param string|DOMNode $expr XPath expression or DOMNode
  * @return boolean
  */
  public function has($expr) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      if ($node instanceof DOMElement &&
          $node->hasChildNodes()) {
        foreach ($node->childNodes as $childNode) {
          if ($expr instanceof DOMNode) {
            if ($expr === $childNode) {
              $result->push($node);
              return $result;
            }
          } elseif ($this->_test($expr, $childNode)) {
            $result->push($node);
          }
        }
      }
    }
    return $result;
  }

  /**
  * Checks the current selection against an expression and returns true,
  * if at least one element of the selection fits the given expression.
  *
  * @example is.php Usage Example: FluentDOM::is()
  * @param string $expr XPath expression
  * @return boolean
  */
  public function is($expr) {
    foreach ($this->_array as $node) {
      return $this->_test($expr, $node);
    }
    return FALSE;
  }

  /**
  * Translate a set of elements in the FluentDOM object into
  * another set of values in an array (which may, or may not contain elements).
  *
  * If the callback function returns an array each element of the array will be added to the
  * result array. All other variable types are put directly into the result array.
  *
  * @example map.php Usage Example: FluentDOM::map()
  * @param callback $function
  * @return array
  */
  public function map($function) {
    $result = array();
    foreach ($this->_array as $index => $node) {
      if ($this->_isCallback($function, TRUE, FALSE)) {
        $mapped = call_user_func($function, $node, $index);
      }
      if ($mapped === NULL) {
        continue;
      } elseif ($mapped instanceof DOMNodeList ||
                $mapped instanceof Iterator ||
                $mapped instanceof IteratorAggregate ||
                is_array($mapped)) {
        foreach ($mapped as $element) {
          if ($element !== NULL) {
            $result[] = $element;
          }
        }
      } else {
        $result[] = $mapped;
      }
    }
    return $result;
  }

  /**
  * Removes elements matching the specified expression from the set of matched elements.
  *
  * @example not.php Usage Example: FluentDOM::not()
  * @param string|callback $expr XPath expression or callback function
  * @return FluentDOM
  */
  public function not($expr) {
    $result = $this->spawn();
    foreach ($this->_array as $index => $node) {
      $check = FALSE;
      if (is_string($expr)) {
        $check = $this->_test($expr, $node, $index);
      } elseif ($this->_isCallback($expr, TRUE, FALSE)) {
        $check = call_user_func($expr, $node, $index);
      }
      if (!$check) {
        $result->push($node);
      }
    }
    return $result;
  }

  /**
  * Selects a subset of the matched elements.
  *
  * @example slice.php Usage Example: FluentDOM::slice()
  * @param integer $start
  * @param integer $end
  * @return FluentDOM
  */
  public function slice($start, $end = NULL) {
    $result = $this->spawn();
    if ($end === NULL) {
      $result->push(array_slice($this->_array, $start));
    } elseif ($end < 0) {
      $result->push(array_slice($this->_array, $start, $end));
    } elseif ($end > $start) {
      $result->push(array_slice($this->_array, $start, $end - $start));
    } else {
      $result->push(array_slice($this->_array, $end, $start - $end));
    }
    return $result;
  }

  /*
  * Traversing - Finding
  */

  /**
  * Adds more elements, matched by the given expression, to the set of matched elements.
  *
  * @example add.php Usage Examples: FluentDOM::add()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function add($expr, $context = NULL) {
    $result = $this->spawn();
    $result->push($this->_array);
    if (isset($context)) {
      $targetNodes = $this->_getTargetNodes($context);
      if (!empty($targetNodes)) {
        foreach ($targetNodes as $node) {
          $result->push($this->_match($expr, $context));
        }
      }
    } elseif (is_object($expr) ||
              (is_string($expr) && substr(ltrim($expr), 0, 1) == '<')) {
      $result->push($this->_getContentNodes($expr));
    } else {
      $result->push($this->find($expr));
    }
    $result->_uniqueSort();
    return $result;
  }

  /**
  * Get a set of elements containing of the unique immediate
  * childnodes including only elements (not textnodes) of each
  * of the matched set of elements.
  *
  * @example children.php Usage Examples: FluentDOM::children()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function children($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      if (empty($expr)) {
        $result->push($node->childNodes, TRUE);
      } else {
        foreach ($node->childNodes as $childNode) {
          if ($this->_test($expr, $childNode)) {
            $result->push($childNode, TRUE);
          }
        }
      }
    }
    $result->_uniqueSort();
    return $result;
  }

  /**
  * Get a set of elements containing all of the unique immediate
  * childnodes including elements and textnodes of each of the matched set of elements.
  *
  * @return FluentDOM
  */
  public function contents() {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $result->push($node->childNodes, FALSE);
    }
    $result->_uniqueSort();
    return $result;
  }

  /**
  * Searches for descendent elements that match the specified expression.
  *
  * @example find.php Usage Example: FluentDOM::find()
  * @param string $expr XPath expression
  * @param boolean $useDocumentContext ignore current node list
  * @return FluentDOM
  */
  public function find($expr, $useDocumentContext = FALSE) {
    $result = $this->spawn();
    if ($useDocumentContext ||
        $this->_useDocumentContext) {
      $result->push($this->_match($expr));
    } else {
      foreach ($this->_array as $contextNode) {
        $result->push($this->_match($expr, $contextNode));
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing the unique next siblings of each of the
  * given set of elements.
  *
  * @example next.php Usage Example: FluentDOM::next()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function next($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $next = $node->nextSibling;
      while ($next instanceof DOMNode && !$this->_isNode($next)) {
        $next = $next->nextSibling;
      }
      if (!empty($next)) {
        if (empty($expr) || $this->_test($expr, $next)) {
          $result->push($next);
        }
      }
    }
    $result->_uniqueSort();
    return $result;
  }

  /**
  * Find all sibling elements after the current element.
  *
  * @example nextAll.php Usage Example: FluentDOM::nextAll()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function nextAll($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $next = $node->nextSibling;
      while ($next instanceof DOMNode) {
        if ($this->_isNode($next)) {
          if (empty($expr) || $this->_test($expr, $next)) {
            $result->push($next);
          }
        }
        $next = $next->nextSibling;
      }
    }
    return $result;
  }

  /**
  * Get all following siblings of each element up to but
  * not including the element matched by the selector.
  *
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function nextUntil($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $next = $node->nextSibling;
      while ($next instanceof DOMNode) {
        if ($this->_isNode($next)) {
          if (isset($expr) && $this->_test($expr, $next)) {
            break;
          } else {
            $result->push($next);
          }
        }
        $next = $next->nextSibling;
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing the unique parents of the matched set of elements.
  *
  * @example parent.php Usage Example: FluentDOM::parent()
  * @return FluentDOM
  */
  public function parent() {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
        $result->push($node->parentNode);
      }
    }
    $result->_uniqueSort();
    return $result;
  }

  /**
  * Get the ancestors of each element in the current set of matched elements,
  * optionally filtered by a selector.
  *
  * @example parents.php Usage Example: FluentDOM::parents()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function parents($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $parents = $this->_match('ancestor::*', $node);
      for ($i = $parents->length - 1; $i >= 0; --$i) {
        $parentNode = $parents->item($i);
        if (empty($expr) || $this->_test($expr, $parentNode)) {
          $result->push($parentNode);
        }
      }
    }
    return $result;
  }

  /**
  * Get the ancestors of each element in the current set of matched elements,
  * up to but not including the element matched by the selector.
  *
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function parentsUntil($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $parents = $this->_match('ancestor::*', $node);
      for ($i = $parents->length - 1; $i >= 0; --$i) {
        $parentNode = $parents->item($i);
        if (!empty($expr) && $this->_test($expr, $parentNode)) {
          break;
        }
        $result->push($parentNode);
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing the unique previous siblings of each of the
  * matched set of elements.
  *
  * @example prev.php Usage Example: FluentDOM::prev()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function prev($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $previous = $node->previousSibling;
      while ($previous instanceof DOMNode && !$this->_isNode($previous)) {
        $previous = $previous->previousSibling;
      }
      if (!empty($previous)) {
        if (empty($expr) || $this->_test($expr, $previous)) {
          $result->push($previous);
        }
      }
    }
    $result->_uniqueSort();
    return $result;
  }

  /**
  * Find all sibling elements in front of the current element.
  *
  * @example prevAll.php Usage Example: FluentDOM::prevAll()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function prevAll($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $previous = $node->previousSibling;
      while ($previous instanceof DOMNode) {
        if ($this->_isNode($previous)) {
          if (empty($expr) || $this->_test($expr, $previous)) {
            $result->push($previous);
          }
        }
        $previous = $previous->previousSibling;
      }
    }
    return $result;
  }

  /**
  * Get all preceding siblings of each element up to but not including
  * the element matched by the selector.
  *
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function prevUntil($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $previous = $node->previousSibling;
      while ($previous instanceof DOMNode) {
        if (isset($expr) && $this->_isNode($previous)) {
          if ($this->_test($expr, $previous)) {
            break;
          } else {
            $result->push($previous);
          }
        }
        $previous = $previous->previousSibling;
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing all of the unique siblings of each of the
  * matched set of elements.
  *
  * @example siblings.php Usage Example: FluentDOM::siblings()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function siblings($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
        foreach ($node->parentNode->childNodes as $childNode) {
          if ($this->_isNode($childNode) &&
              $childNode !== $node) {
            if (empty($expr) || $this->_test($expr, $childNode)) {
              $result->push($childNode);
            }
          }
        }
      }
    }
    $result->_uniqueSort();
    return $result;
  }

  /**
  * Get a set of elements containing the closest parent element that matches the specified
  * selector, the starting element included.
  *
  * @example closest.php Usage Example: FluentDOM::closest()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function closest($expr, $context = NULL) {
    $result = $this->spawn();
    $context = $this->_getContextNodes($context);
    foreach ($context as $node) {
      while (isset($node)) {
        if ($this->_test($expr, $node)) {
          $result->push($node);
          break;
        }
        $node = $node->parentNode;
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing only the first of the currently selected elements.
  *
  * @return FluentDOM
  */
  public function first() {
    return $this->eq(0);
  }

  /**
  * Get a set of elements containing only the last of the currently selected elements.
  *
  * @return FluentDOM
  */
  public function last() {
    return $this->eq(-1);
  }

  /*
  * Traversing - Chaining
  */

  /**
  * Add the previous selection to the current selection.
  *
  * @return FluentDOM
  */
  public function andSelf() {
    $result = $this->spawn();
    $result->push($this->_array);
    $result->push($this->_parent);
    return $result;
  }

  /**
  * Revert the most recent traversing operation,
  * changing the set of matched elements to its previous state.
  *
  * @return FluentDOM
  */
  public function end() {
    if ($this->_parent instanceof FluentDOM) {
      return $this->_parent;
    } else {
      return $this;
    }
  }

  /**
  * Reverse the order of the matched elements.
  *
  * @return FluentDOM
  */
  public function reverse() {
    $result = $this->spawn();
    $result->push(array_reverse($this->_array));
    return $result;
  }

  /*
  * Manipulation - Changing Contents
  */

  /**
  * Get or set the xml contents of the first matched element.
  *
  * @example xml.php Usage Example: FluentDOM::xml()
  * @param string|Callback|Closure $xml XML fragment
  * @return string|FluentDOM
  */
  public function xml($xml = NULL) {
    if (isset($xml)) {
      $isCallback = $this->_isCallback($xml, FALSE, TRUE);
      if ($isCallback) {
        foreach ($this->_array as $index => $node) {
          $xmlString = call_user_func(
            $xml,
            $node,
            $index,
            $this->_getInnerXml($node)
          );
          $node->nodeValue = '';
          if (!empty($xmlString)) {
            $fragment = $this->_getContentFragment($xmlString, TRUE);
            foreach ($fragment as $contentNode) {
              $node->appendChild($contentNode->cloneNode(TRUE));
            }
          }
        }
      } else {
        if (!empty($xml)) {
          $fragment = $this->_getContentFragment($xml, TRUE);
        } else {
          $fragment = array();
        }
        foreach ($this->_array as $node) {
          $node->nodeValue = '';
          foreach ($fragment as $contentNode) {
            $node->appendChild($contentNode->cloneNode(TRUE));
          }
        }
      }
      return $this;
    } else {
      if (isset($this->_array[0])) {
        return $this->_getInnerXml($this->_array[0]);
      }
      return '';
    }
  }

  /**
  * Get the combined text contents of all matched elements or
  * set the text contents of all matched elements.
  *
  * @example text.php Usage Example: FluentDOM::text()
  * @param string|callback|Closure $text
  * @return string|FluentDOM
  */
  public function text($text = NULL) {
    if (isset($text)) {
      $isCallback = $this->_isCallback($text, FALSE, TRUE);
      foreach ($this->_array as $index => $node) {
        if ($isCallback) {
          $node->nodeValue = call_user_func($text, $node, $index, $node->nodeValue);
        } else {
          $node->nodeValue = $text;
        }
      }
      return $this;
    } else {
      $result = '';
      foreach ($this->_array as $node) {
        $result .= $node->textContent;
      }
      return $result;
    }
  }

  /*
  * Manipulation - Inserting Inside
  */

  /**
  * Append content to the inside of every matched element.
  *
  * @example append.php Usage Example: FluentDOM::append()
  * @param string|array|DOMNode|Iterator $content DOMNode or DOMNodeList or xml fragment string
  * @return FluentDOM
  */
  public function append($content) {
    $result = $this->spawn();
    if (empty($this->_array) &&
        $this->_useDocumentContext &&
        !isset($this->_document->documentElement)) {
      if ($this->_isCallback($content, FALSE, TRUE)) {
        $contentNode = $this->_getContentElement(
          $this->_executeEasySetter($content, NULL, 0, '')
        );
      } else {
        $contentNode = $this->_getContentElement($content);
      }
      $result->push($this->_document->appendChild($contentNode));
    } else {
      $result->push(
        $this->_applyContentToNodes(
          $this->_array,
          $content,
          array($this->_getHandler(), 'appendChildren')
        )
      );
    }
    return $result;
  }

  /**
  * Append all of the matched elements to another, specified, set of elements.
  * Returns all of the inserted elements.
  *
  * @example appendTo.php Usage Example: FluentDOM::appendTo()
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @return FluentDOM
  */
  public function appendTo($selector) {
    $result = $this->spawn();
    $targetNodes = $this->_getTargetNodes($selector);
    if (!empty($targetNodes)) {
      $result->push(
        $this->_applyContentToNodes(
          $targetNodes,
          $this->_array,
          array($this->_getHandler(), 'appendChildren')
        )
      );
      $this->_removeNodes($this->_array);
    }
    return $result;
  }

  /**
  * Prepend content to the inside of every matched element.
  *
  * @example prepend.php Usage Example: FluentDOM::prepend()
  * @param string|array|DOMNode|Iterator $content
  * @return FluentDOM
  */
  public function prepend($content) {
    $result = $this->spawn();
    $result->push(
      $this->_applyContentToNodes(
        $this->_array,
        $content,
        array($this->_getHandler(), 'insertChildrenBefore')
      )
    );
    return $result;
  }

  /**
  * Prepend all of the matched elements to another, specified, set of elements.
  * Returns all of the inserted elements.
  *
  * @example prependTo.php Usage Example: FluentDOM::prependTo()
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @return FluentDOM list of all new elements
  */
  public function prependTo($selector) {
    $result = $this->spawn();
    $targetNodes = $this->_getTargetNodes($selector);
    if (!empty($targetNodes)) {
      $result->push(
        $this->_applyContentToNodes(
          $targetNodes,
          $this->_array,
          array($this->_getHandler(), 'insertChildrenBefore')
        )
      );
      $this->_removeNodes($this->_array);
    }
    return $result;
  }

  /*
  * Manipulation - Inserting Outside
  */

  /**
  * Insert content after each of the matched elements.
  *
  * @example after.php Usage Example: FluentDOM::after()
  * @param string|array|DOMNode|DOMNodeList|Iterator|callback|Closure $content
  * @return FluentDOM
  */
  public function after($content) {
    $result = $this->spawn();
    $result->push(
      $this->_applyContentToNodes(
        $this->_array, $content, array($this->_getHandler(), 'insertNodesAfter')
      )
    );
    return $result;
  }

  /**
  * Insert content before each of the matched elements.
  *
  * @example before.php Usage Example: FluentDOM::before()
  * @param string|array|DOMNode|DOMNodeList|Iterator|callback|Closure $content
  * @return FluentDOM
  */
  public function before($content) {
    $result = $this->spawn();
    $result->push(
      $this->_applyContentToNodes(
        $this->_array, $content, array($this->_getHandler(), 'insertNodesBefore')
      )
    );
    return $result;
  }

  /**
  * Insert all of the matched elements after another, specified, set of elements.
  *
  * @example insertAfter.php Usage Example: FluentDOM::insertAfter()
  * @param string|array|DOMNode|DOMNodeList|Iterator $selector
  * @return FluentDOM
  */
  public function insertAfter($selector) {
    $result = $this->spawn();
    $targetNodes = $this->_getTargetNodes($selector);
    if (!empty($targetNodes)) {
      $result->push(
        $this->_applyContentToNodes(
          $targetNodes,
          $this->_array,
          array($this->_getHandler(), 'insertNodesAfter')
        )
      );
      $this->_removeNodes($this->_array);
    }
    return $result;
  }

  /**
  * Insert all of the matched elements before another, specified, set of elements.
  *
  * @example insertBefore.php Usage Example: FluentDOM::insertBefore()
  * @param string|array|DOMNode|DOMNodeList|Iterator $selector
  * @return FluentDOM
  */
  public function insertBefore($selector) {
    $result = $this->spawn();
    $targetNodes = $this->_getTargetNodes($selector);
    if (!empty($targetNodes)) {
      $result->push(
        $this->_applyContentToNodes(
          $targetNodes,
          $this->_array,
          array($this->_getHandler(), 'insertNodesBefore')
        )
      );
      $this->_removeNodes($this->_array);
    }
    return $result;
  }

  /*
  * Manipulation - Inserting Around
  */

  /**
  * Wrap $content around a set of elements
  *
  * @param array $elements
  * @param string|array|DOMNode|DOMNodeList|Iterator|callback|Closure $content
  * @return FluentDOM
  */
  protected function _wrap($elements, $content) {
    $result = array();
    $isCallback = $this->_isCallback($content, FALSE, TRUE);
    if (!$isCallback) {
      $wrapperTemplate = $this->_getContentElement($content);
    }
    $simple = FALSE;
    foreach ($elements as $index => $node) {
      if ($isCallback) {
        $wrapperTemplate = NULL;
        $wrapContent = call_user_func($content, $node, $index);
        if (!empty($wrapContent)) {
          $wrapperTemplate = $this->_getContentElement($wrapContent);
        }
      }
      if ($wrapperTemplate instanceof DOMElement) {
        $wrapper = $wrapperTemplate->cloneNode(TRUE);
        if (!$simple) {
          $targets = $this->_match('.//*[count(*) = 0]', $wrapper);
        }
        if ($simple || $targets->length == 0) {
          $target = $wrapper;
          $simple = TRUE;
        } else {
          $target = $targets->item(0);
        }
        if (isset($node->parentNode)) {
          $node->parentNode->insertBefore($wrapper, $node);
        }
        $target->appendChild($node);
        $result[] = $node;
      }
    }
    return $result;
  }

  /**
  * Wrap each matched element with the specified content.
  *
  * If $content contains several elements the first one is used
  *
  * @example wrap.php Usage Example: FluentDOM::wrap()
  * @param string|array|DOMNode|DOMNodeList|Iterator|callback|Closure $content
  * @return FluentDOM
  */
  public function wrap($content) {
    $result = $this->spawn();
    $result->push($this->_wrap($this->_array, $content));
    return $result;
  }

  /**
  * Wrap al matched elements with the specified content
  *
  * If the matched elemetns are not siblings, wrap each group of siblings.
  *
  * @example wrapAll.php Usage Example: FluentDOM::wrapAll()
  * @param string|array|DOMNode|Iterator $content
  * @return FluentDOM
  */
  public function wrapAll($content) {
    $result = $this->spawn();
    $current = NULL;
    $counter = 0;
    $groups = array();
    //group elements by previous node - ignore whitespace text nodes
    foreach ($this->_array as $node) {
      $previous = $node->previousSibling;
      while ($previous instanceof DOMText && $previous->isWhitespaceInElementContent()) {
        $previous = $previous->previousSibling;
      }
      if ($previous !== $current) {
        $counter++;
      }
      $groups[$counter][] = $node;
      $current = $node;
    }
    if (count($groups) > 0) {
      $wrapperTemplate = $this->_getContentElement($content);
      $simple = FALSE;
      foreach ($groups as $group) {
        if (isset($group[0])) {
          $node = $group[0];
          $wrapper = $wrapperTemplate->cloneNode(TRUE);
          if (!$simple) {
            $targets = $this->_match('.//*[count(*) = 0]', $wrapper);
          }
          if ($simple || $targets->length == 0) {
            $target = $wrapper;
            $simple = TRUE;
          } else {
            $target = $targets->item(0);
          }
          if (isset($node->parentNode)) {
            $node->parentNode->insertBefore($wrapper, $node);
          }
          foreach ($group as $node) {
            $target->appendChild($node);
          }
          $result->push($node);
        }
      }
    }
    return $result;
  }

  /**
  * Wrap the inner child contents of each matched element
  * (including text nodes) with an XML structure.
  *
  * @example wrapInner.php Usage Example: FluentDOM::wrapInner()
  * @param string|array|DOMNode|DOMNodeList|Iterator $content
  * @return FluentDOM
  */
  public function wrapInner($content) {
    $result = $this->spawn();
    $elements = array();
    foreach ($this->_array as $node) {
      foreach ($node->childNodes as $childNode) {
        if ($this->_isNode($childNode)) {
          $elements[] = $childNode;
        }
      }
    }
    $result->push($this->_wrap($elements, $content));
    return $result;
  }

  /*
  * Manipulation - Replacing
  */

  /**
  * Replaces all matched elements with the specified HTML or DOM elements.
  * This returns the JQuery element that was just replaced,
  * which has been removed from the DOM.
  *
  * @example replaceWith.php Usage Example: FluentDOM::replaceWith()
  * @param string|array|DOMNode|DOMNodeList|Iterator|callback|Closure $content
  * @return FluentDOM
  */
  public function replaceWith($content) {
    $this->_applyContentToNodes(
      $this->_array, $content, array($this->_getHandler(), 'insertNodesBefore')
    );
    $this->_removeNodes($this->_array);
    return $this;
  }


  /**
  * Replaces the elements matched by the specified selector with the matched elements.
  *
  * @example replaceAll.php Usage Example: FluentDOM::replaceAll()
  * @param string|array|DOMNode|DOMNodeList|Iterator $selector
  * @return FluentDOM
  */
  public function replaceAll($selector) {
    $result = $this->spawn();
    $targetNodes = $this->_getTargetNodes($selector);
    if (!empty($targetNodes)) {
      $this->_applyContentToNodes(
        $targetNodes,
        $this->_array,
        array($this->_getHandler(), 'insertNodesBefore')
      );
      $this->_removeNodes($targetNodes);
    }
    $this->_removeNodes($this->_array);
    return $result;
  }

  /*
  * Manipulation - Removing
  */

  /**
  * Remove all child nodes from the set of matched elements.
  *
  * This is the empty() method - but because empty
  * is a reserved word we can no declare it directly
  * @see __call
  *
  * @example empty.php Usage Example: FluentDOM:empty()
  * @return FluentDOM
  */
  protected function _emptyNodes() {
    foreach ($this->_array as $node) {
      if ($node instanceof DOMElement ||
          $node instanceof DOMText) {
        $node->nodeValue = '';
      }
    }
    return $this;
  }

  /**
  * Removes all matched elements from the DOM.
  *
  * @example remove.php Usage Example: FluentDOM::remove()
  * @param string $expr XPath expression
  * @return FluentDOM removed elements
  */
  public function remove($expr = NULL) {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
        if (empty($expr) || $this->_test($expr, $node)) {
          $result->push($node->parentNode->removeChild($node));
        }
      }
    }
    return $result;
  }

  /*
  * Manipulation - Creation
  */

  /*
  * Manipulation - Copying
  */

  /**
  * Clone matched DOM Elements and select the clones.
  *
  * This is the clone() method - but because clone
  * is a reserved word we can no declare it directly
  * @see __call
  *
  * @example clone.php Usage Example: FluentDOM:clone()
  * @return FluentDOM
  */
  protected function _cloneNodes() {
    $result = $this->spawn();
    foreach ($this->_array as $node) {
      $result->push($node->cloneNode(TRUE));
    }
    return $result;
  }

  /*
  * Attributes - General
  */

  /**
  * Access a property on the first matched element or set the attribute(s) of all matched elements
  *
  * @example attr.php Usage Example: FluentDOM:attr() Read an attribute value.
  * @param string|array $attribute attribute name or attribute list
  * @param string|callback|Closure $value function callback($index, $value) or value
  * @return string|FluentDOM attribute value or $this
  */
  public function attr($attribute, $value = NULL) {
    if (is_array($attribute) && count($attribute) > 0) {
      //expr is an array of attributes and values - set on each element
      foreach ($attribute as $key => $value) {
        if ($this->_isQName($key)) {
          foreach ($this->_array as $node) {
            if ($node instanceof DOMElement) {
              $node->setAttribute($key, $value);
            }
          }
        }
      }
    } elseif (is_null($value)) {
      //empty value - read attribute from first element in list
      if ($this->_isQName($attribute) &&
          count($this->_array) > 0) {
        $node = $this->_array[0];
        if ($node instanceof DOMElement) {
          return $node->getAttribute($attribute);
        }
      }
      return NULL;
    } elseif (is_array($value) ||
              $value instanceof Closure) {
      //value is function callback - execute it and set result on each element
      if ($this->_isQName($attribute)) {
        foreach ($this->_array as $index => $node) {
          if ($node instanceof DOMElement) {
            $newValue =
            $node->setAttribute(
              $attribute,
              call_user_func($value, $node, $index, $node->getAttribute($attribute))
            );
          }
        }
      }
    } else {
      // set attribute value of each element
      if ($this->_isQName($attribute)) {
        foreach ($this->_array as $node) {
          if ($node instanceof DOMElement) {
            $node->setAttribute($attribute, (string)$value);
          }
        }
      }
    }
    return $this;
  }

  /**
  * Remove an attribute from each of the matched elements.
  *
  * @example removeAttr.php Usage Example: FluentDOM::removeAttr()
  * @param string $name
  * @return FluentDOM
  */
  public function removeAttr($name) {
    if (!empty($name)) {
      if (is_string($name) && $name !== '*') {
        $attributes = array($name);
      } elseif (is_array($name)) {
        $attributes = $name;
      } elseif ($name !== '*') {
        throw new InvalidArgumentException();
      }
      foreach ($this->_array as $node) {
        if ($node instanceof DOMElement) {
          if ($name === '*') {
            for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
              $node->removeAttribute($node->attributes->item($i)->name);
            }
          } else {
            foreach ($attributes as $attribute) {
              if ($node->hasAttribute($attribute)) {
                $node->removeAttribute($attribute);
              }
            }
          }
        }
      }
    }
    return $this;
  }

  /**
  * Read a data attribute from the first node or set data attributes n all lesected nodes.
  *
  * @param string|array $name data attribute identifier or array of data attributes to set
  * @param mixed $value
  * @return mixed
  */
  public function data($name, $value = NULL) {
    if (!is_array($name) && is_null($value)) {
      //reading
      if (isset($this->_array[0]) &&
          $this->_array[0] instanceof DOMElement) {
        $data = new FluentDOMData($this->_array[0]);
        return $data->$name;
      }
      return NULL;
    } elseif (is_array($name)) {
      $values = $name;
    } else {
      $values = array((string)$name => $value);
    }
    foreach ($this->_array as $node) {
      if ($node instanceof DOMElement) {
        $data = new FluentDOMData($node);
        foreach ($values as $dataName => $dataValue) {
          $data->$dataName = $dataValue;
        }
      }
    }
  }

  /*
  * Attributes - Classes
  */

  /**
  * Adds the specified class(es) to each of the set of matched elements.
  *
  * @param string|callback|Closure $class
  * @return FluentDOM
  */
  public function addClass($class) {
    return $this->toggleClass($class, TRUE);
  }

  /**
  * Returns true if the specified class is present on at least one of the set of matched elements.
  *
  * @param string|callback|Closure $class
  * @return boolean
  */
  public function hasClass($class) {
    foreach ($this->_array as $node) {
      if ($node instanceof DOMElement &&
          $node->hasAttribute('class')) {
        $classes = preg_split('(\s+)', trim($node->getAttribute('class')));
        if (in_array($class, $classes)) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
  * Removes all or the specified class(es) from the set of matched elements.
  *
  * @param string|callback|Closure $class
  * @return FluentDOM
  */
  public function removeClass($class = '') {
    return $this->toggleClass($class, FALSE);
  }

  /**
  * Adds the specified class if the switch is TRUE,
  * removes the specified class if the switch is FALSE,
  * toggles the specified class if the switch is NULL.
  *
  * @example toggleClass.php Usage Example: FluentDOM::toggleClass()
  * @param string|callback|Closure $class
  * @param NULL|boolean $switch toggle if NULL, add if TRUE, remove if FALSE
  * @return FluentDOM
  */
  public function toggleClass($class, $switch = NULL) {
    foreach ($this->_array as $index => $node) {
      if ($node instanceof DOMElement) {
        $isCallback = $this->_isCallback($class, FALSE, TRUE);
        if ($isCallback) {
          $classString = call_user_func(
            $class, $node, $index, $node->getAttribute('class')
          );
        } else {
          $classString = $class;
        }
        if (empty($classString) && $switch == FALSE) {
          if ($node->hasAttribute('class')) {
            $node->removeAttribute('class');
          }
        } else {
          if ($node->hasAttribute('class')) {
            $currentClasses = array_flip(
              preg_split('(\s+)', trim($node->getAttribute('class')))
            );
          } else {
            $currentClasses = array();
          }
          $toggledClasses = array_unique(preg_split('(\s+)', trim($classString)));
          $modified = FALSE;
          foreach ($toggledClasses as $toggledClass) {
            if (isset($currentClasses[$toggledClass])) {
              if ($switch === FALSE || is_null($switch)) {
                unset($currentClasses[$toggledClass]);
                $modified = TRUE;
              }
            } else {
              if ($switch === TRUE || is_null($switch)) {
                $currentClasses[$toggledClass] = TRUE;
                $modified = TRUE;
              }
            }
          }
          if ($modified) {
            if (empty($currentClasses)) {
              $node->removeAttribute('class');
            } else {
              $node->setAttribute('class', implode(' ', array_keys($currentClasses)));
            }
          }
        }
      }
    }
    return $this;
  }
}