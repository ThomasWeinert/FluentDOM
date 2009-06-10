<?php
/**
* FluentDOM implements a jQuery like replacement for DOMNodeList
*
* @version $Id$
*/

/**
* Function to create a new FluentDOM instance
*
* This is a shortcut for "new FluentDOM($source)"
*
* @param mixed $source
* @access public
* @return FluentDOM
*/
function FluentDOM($source) {
  return new FluentDOM($source);
}

/**
* FluentDOM implements a jQuery like replacement for DOMNodeList
*/
class FluentDOM implements RecursiveIterator, SeekableIterator, Countable, ArrayAccess {

  /**
  * document object
  * @var object DOMDocument
  * @access private
  */
  private $_document = NULL;

  /**
  * use document context for expression
  * @var boolean
  * @access private
  */
  private $_useDocumentContext = FALSE;

  /**
  * parent node list (last selection in chain)
  * @var object FluentDOM
  * @access private
  */
  private $_parent = NULL;

  /**
  * current iterator position
  * @var integer
  * @access private
  */
  private $_position = 0;

  /**
  * element nodes
  * @var array
  * @access private
  */
  private $_array = array();

  /**
  * internal xpath instance
  * @var object DOMXPath
  * @access private
  */
  private $_xpath = NULL;

  /**
  * initialize the FluentDOM instance and set private properties
  *
  * @param object FluentDOM | object DOMElement | object DOMDocument $source
  * source to create FluentDOM from
  * @access public
  */
  public function __construct($source) {
    if ($source instanceof FluentDOM) {
      $this->_document = $source->document;
      $this->_xpath = $source->_xpath;
      $this->_parent = $source;
    } elseif ($source instanceof DOMDocument) {
      $this->_document = $source;
      $this->_useDocumentContext = TRUE;
    } elseif ($this->_isNode($source)) {
      $this->_document = $source->ownerDocument;
      $this->_push($source);
    } elseif (is_string($source)) {
      $this->_document = new DOMDocument();
      $this->_document->loadXML($source);
      $this->_useDocumentContext = TRUE;
    } else {
      throw new InvalidArgumentException('Invalid source object.');
    }
  }

  /**
  * implement dynamic properties using magic methods
  *
  * @param string $name
  * @access public
  * @return mixed
  */
  public function __get($name) {
    switch ($name) {
    case 'length' :
      return count($this->_array);
    case 'document' :
      return $this->_document;
    case 'xpath' :
      return $this->_xpath();
    default :
      return NULL;
    }
  }

  /**
  * block changes of dynamic readonly property length
  *
  * @param $name
  * @param $value
  * @access public
  * @return void
  */
  public function __set($name, $value) {
    switch ($name) {
    case 'length' :
    case 'document' :
    case 'xpath' :
      throw new BadMethodCallException('Can not set readonly value.');
      break;
    default :
      $this->$name = $value;
      break;
    }
  }

  /**
  * support isst for dynic properties length and document
  *
  * @param $name
  * @access public
  * @return boolean
  */
  public function __isset($name) {
    switch ($name) {
    case 'length' :
    case 'xpath' :
      return TRUE;
    case 'document' :
      return isset($this->_document);
    }
    return FALSE;
  }
  
  /**
  * declaring an empty() method will crash the parser so we use some magic
  *
  * @param string $name
  * @param array $arguments
  * @access public
  * @return mixed
  */
  public function __call($name, $arguments) {
    switch (strtolower($name)) {
    case 'empty' :
      return $this->_emptyNodes();
    case 'clone' :
      return $this->_cloneNodes();
    }
  }
  
  /**
  * Return the XML output of the internal dom document
  *
  * @access public
  * @return string
  */
  public function __toString() {
    return $this->_document->saveXML();
  }

  /**
  * the item() method is used to access elements in the node list
  *
  * @param $position
  * @access public
  * @return object DOMNode
  */
  public function item($position) {
    if (isset($this->_array[$position])) {
      return $this->_array[$position];
    }
    return NULL;
  }
  
  /*
  * Interface - Iterator, SeekableIterator
  */

  /**
  * Get current iterator element
  *
  * @access public
  * @return DOMNode
  */
  public function current() {
    return $this->_array[$this->_position];
  }

  /**
  * Get current iterator pointer
  *
  * @access public
  * @return integer
  */
  public function key() {
    return $this->_position;
  }

  /**
  * Move iterator pointer to next element
  *
  * @access public
  * @return void
  */
  public function next() {
    ++$this->_position;
  }

  /**
  * Reset iterator pointer
  *
  * @access public
  * @return void
  */
  public function rewind() {
    $this->_position = 0;
  }

  /**
  * Move iterator pointer to specified element
  *
  * @param integer $position
  * @access public
  * @return void
  */
  public function seek($position) {
    if (isset($this->_array[$position])) {
      $this->_position = $position;
    } else {
      throw new InvalidArgumentException('Unknown position');
    }
  }

  /**
  * Check if current iterator pointer contains a valid element
  *
  * @access public
  * @return boolean
  */
  public function valid() {
    return isset($this->_array[$this->_position]);
  }
  
  /**
  * Get children of the current iterator element
  *
  * @access public
  * @return object FluentDOM
  */
  public function getChildren() {
    $result = $this->_spawn();
    $result->_push($this->_match('node()', $this->_array[$this->_position]));
    return $result;
  }
  
  /**
  * Check if the current iterator element has children
  *
  * @access public
  * @return object FluentDOM
  */
  public function hasChildren() {
    return $this->_test('node()', $this->_array[$this->_position]);
  }
  
  /*
  * Interface - Countable
  */

  /**
  * get element count (Countable)
  *
  * @access public
  * @return
  */
  public function count() {
    return count($this->_array);
  }
  
  /*
  * Interface - ArrayAccess
  */
  
  /**
  * If somebody tries to modify the internal array throw an exception.
  *
  * @param integer $offset
  * @param mixed $value
  * @access public
  * @return void
  */
  public function offsetSet($offset, $value) {
    throw new BadMethodCallException('List is read only');
  }
  
  /**
  * Check if index exists in internal array
  *
  * @param integer $offset
  * @access public
  * @return boolean
  */
  public function offsetExists($offset) {
    return isset($this->_array[$offset]);
  }
  
  /**
  * If somebody tries to remove an element from the internal array throw an exception.
  *
  * @param integer $offset
  * @access public
  * @return void
  */
  public function offsetUnset($offset) {
    throw new BadMethodCallException('List is read only');
  }
  
  /**
  * Get element from internal array
  *
  * @param $offset
  * @access public
  * @return void
  */
  public function offsetGet($offset) {
    return isset($this->_array[$offset]) ? $this->_array[$offset] : null;
  }
  
  /*
  * Core functions
  */
  
  /**
  * Create a new instance of the same class with the $this as the parent.
  *
  * This is used for the chaining and needs to be overloaded in child classes.
  *
  * @access private
  * @return
  */
  private function _spawn() {
    return new FluentDOM($this);
  }

  /**
  * create a new xpath object an register default namespaces from the current document
  *
  * @access private
  * @return object DOMXPath
  */
  private function _xpath() {
    if (empty($this->_xpath) || $this->_xpath->document != $this->_document) {
      $this->_xpath = new DOMXPath($this->_document);
      if ($this->_document->documentElement) {
        $uri = $this->_document->documentElement->lookupnamespaceURI('_');
        if (!isset($uri)) {
          $uri = $this->_document->documentElement->lookupnamespaceURI(NULL);
          if (isset($uri)) {
            $this->_xpath->registerNamespace('_', $uri);
          }
        }
      }
    }
    return $this->_xpath;
  }

  /**
  * match xpath expression agains context and return matched elements
  *
  * @param string$expr
  * @param DOMElement $context optional, default value NULL
  * @access private
  * @return DOMNodeList
  */
  private function _match($expr, $context = NULL) {
    if (isset($context)) {
      return $this->_xpath()->query($expr, $context);
    } else {
      return $this->_xpath()->query($expr);
    }
  }

  /**
  * test xpath expression against context and return true/false
  *
  * @param string$expr
  * @param DOMElement $context optional, default value NULL
  * @access private
  * @return boolean
  */
  private function _test($expr, $context = NULL) {
    if (isset($context)) {
      $check = $this->_xpath()->evaluate($expr, $context);
    } else {
      $check = $this->_xpath()->evaluate($expr);
    }
    if ($check instanceof DOMNodeList) {
      return $check->length > 0;
    } else {
      return (bool)$check;
    }
  }

  /**
  * push new elements an the list
  *
  * @param object DOMElement | object DOMNodeList | object FluentDOM $elements
  * @access private
  * @return void
  */
  private function _push($elements, $unique = FALSE) {
    if ($this->_isNode($elements)) {
      if ($elements->ownerDocument === $this->_document) {
        if (!$unique || !$this->_inList($elements, $this->_array)) {
          $this->_array[] = $elements;
        }
      } else {
        throw new OutOfBoundsException('Node is not a part of this document');
      }
    } elseif ($elements instanceof DOMNodeList ||
              $elements instanceof DOMDocumentFragment ||
              $elements instanceof Iterator ||
              is_array($elements)) {
      foreach ($elements as $node) {
        if ($this->_isNode($node)) {
          if ($node->ownerDocument === $this->_document) {
            if (!$unique || !$this->_inList($node, $this->_array)) {
              $this->_array[] = $node;
            }
          } else {
            throw new OutOfBoundsException('Node is not a part of this document');
          }
        }
      }
    }
  }

  /**
  * check if object is already in internal list
  *
  * @param object DOMElement $node
  * @access private
  * @return boolean
  */
  private function _inList($node) {
    foreach ($this->_array as $compareNode) {
      if ($compareNode === $node) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
  * validate string as qualified tag name
  *
  * @todo implement isQName
  * @param string $name
  * @access private
  * @return boolean
  */
  private function _isQName($name) {
    return TRUE;
  }
  
  /**
  * Check if the DOMNode is DOMElement or DOMText with content
  *
  * @param DOMNode $node
  * @access private
  * @return boolean
  */
  private function _isNode($node) {
    if (is_object($node)) {
      if ($node instanceof DOMElement) {
        return TRUE;
      } elseif ($node instanceof DOMText && 
                !$node->isWhitespaceInElementContent()) {
        return TRUE;
      }
    }
    return FALSE;
  }
  
  /**
  * Convert a given content into and array of nodes
  *
  * @param string | object DOMElement | object DOMText | object Iterator $content
  * @param boolean $includeTextNodes
  * @param integer $limit
  * @access private
  * @return array
  */
  private function _getContentNodes($content, $includeTextNodes = TRUE, $limit = 0) {
    $result = array();
    if ($content instanceof DOMElement) {
      $result = array($content);
    } elseif ($includeTextNodes && $this->_isNode($content)) {
      $result = array($content);
    } elseif (is_string($content)) {
      $fragment = $this->_document->createDocumentFragment();
      if ($fragment->appendXML($content)) {
        foreach ($fragment->childNodes as $element) {
          if ($element instanceof DOMElement ||
              ($includeTextNodes && $this->_isNode($element))) {
            $element->parentNode->removeChild($element);
            $result[] = $element;
            if ($limit > 0 && count($result) > $limit) {
              break;
            }
          }
        }
        return $result;
      } else {
        throw new UnexpectedValueException('Invalid document fragment');
      }
    } elseif ($content instanceof DOMNodeList ||
              $content instanceof Iterator ||
              is_array($content)) {
      foreach ($content as $element) {
        if ($element instanceof DOMElement ||
            ($includeTextNodes && $this->_isNode($element))) {
          $result[] = $element;
          if ($limit > 0 && count($result) > $limit) {
            break;
          }  
        } 
      }
    }
    if (empty($result)) {
      throw new UnexpectedValueException('No element found'); 
    } else {
      //if a node is not in the current document import it
      foreach ($result as $index => $node) {
        if ($node->ownerDocument !== $this->_document) {
          $result[$index] = $this->_document->importNode($node, TRUE);
        }
      }
    }
    return $result;
  }
  
  private function _getTargetNodes($selector) {
    if ($this->_isNode($selector)) {
      return array($selector);
    } elseif (is_string($selector)) {
      return $this->_match($selector);
    } elseif (is_array($selector) ||
              $selector instanceof Iterator ||
              $selector instanceof DOMNodeList) {
      return $selector;
    } else {
      throw new InvalidArgumentException('Invalid selector');
    }
  }
  
  /**
  * Remove nodes from document tree
  *
  * @param $selector
  * @access private
  * @return array removed nodes
  */
  private function _removeNodes($selector) {
    $targetNodes = $this->_getTargetNodes($selector);
    $result = array();
    foreach ($targetNodes as $node) {
      if ($node instanceof DOMNode &&
          isset($node->parentNode)) {
        $result[] = $node->parentNode->removeChild($node);
      }
    }
    return $result;
  }
  
  /**
  * Convert content to DOMElement
  *
  * @param string | array | object DOMElement | object FluentDOM $content
  * @access private
  * @return object DOMElement
  */
  private function _getContentElement($content) {
    if ($content instanceof DOMElement) {
      return $content;
    } elseif (is_string($content) ||
              is_array($content) ||
              $content instanceof DOMNodeList ||
              $content instanceof Iterator) {
      $contentNodes = $this->_getContentNodes($content, FALSE, 1);
      return $contentNodes[0];
    }
  }

  /*
  * Object Accessors
  */

  /**
  * Execute a function within the context of every matched element.
  *
  * @param callback | object Closure $function
  * @access public
  * @return object FluentDOM
  */
  public function each($function) {
    if (is_array($function) ||
        is_string($function) ||
        $function instanceof Closure) {
      foreach ($this->_array as $index => $node) {
        call_user_func($function, $node, $index);
      }
    } else {
      throw new InvalidArgumentException('Invalid callback function');
    }
    return $this;
  }
  
  /**
  * Formats the current document, resets internal node array and other properties.
  *
  * The document is saved and reloaded, all variables with DOMNodes of this document will get invalid.
  *
  * @access public
  * @return object FluentDOM
  */
  public function formatOutput() {
    $this->_array = array();
    $this->_position = 0;
    $this->_useDocumentContext = TRUE;
    $this->_parent = NULL;
    $this->_document->preserveWhiteSpace = FALSE;
    $this->_document->formatOutput = TRUE;
    $this->_document->loadXML($this->_document->saveXML());
    return $this;
  }

  /*
  * Traversing - Filtering
  */

  /**
  * Reduce the set of matched elements to a single element.
  *
  * @param integer $position Element index (start with 0)
  * @access public
  * @return object FluentDOM
  */
  public function eq($position) {
    $result = $this->_spawn();
    if (isset($this->_array[$position])) {
      $result->_push($this->_array[$position]);
    }
    return $result;
  }

  /**
  * Removes all elements from the set of matched elements that do not match the specified expression(s).
  *
  * @param string $expr | callback | object Closure XPath expression or callback function
  * @access public
  * @return object FluentDOM
  */
  public function filter($expr) {
    $result = $this->_spawn();
    foreach ($this->_array as $index => $node) {
      if (is_string($expr)) {
        $check = $this->_test($expr, $node);
      } elseif ($expr instanceof Closure ||
                is_array($expr)) {
        $check = call_user_func($expr, $node, $index);
      } else {
        $check = TRUE;
      }
      if ($check) {
        $result->_push($node);
      }
    }
    return $result;
  }

  /**
  * Checks the current selection against an expression and returns true,
  * if at least one element of the selection fits the given expression.
  *
  * @param string $expr XPath expression
  * @access public
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
  * @param callback | object Closure $function
  * @access public
  * @return array
  */
  public function map($function) {
    $result = array();
    foreach ($this->_array as $index => $node) {
      if (is_array($function) ||
          is_string($function) ||
          $function instanceof Closure) {
        $mapped = call_user_func($function, $node, $index);
      } else {
        throw new InvalidArgumentException('Invalid callback function');
      }
      if ($mapped === NULL) {
        continue;
      } elseif ($mapped instanceof DOMNodeList ||
                $mapped instanceof Iterator ||
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
  * @param string $expr | callback | object Closure XPath expression or callback function
  * @access public
  * @return object FluentDOM
  */
  public function not($expr) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      if (is_string($expr)) {
        $check = $this->_test($expr, $node);
      } elseif ($expr instanceof Closure ||
                is_array($expr)) {
        $check = call_user_func($expr, $node, $index);
      } else {
        $check = TRUE;
      }
      if (!$check) {
        $result->_push($node);
      }
    }
    return $result;
  }

  /**
  * Selects a subset of the matched elements.
  *
  * @param integer $start
  * @param integer $end
  * @access public
  * @return object FluentDOM
  */
  function slice($start, $end = NULL) {
    $result = $this->_spawn();
    if ($end === NULL) {
      $result->_push(array_slice($this->_array, $start));
    } elseif ($end < 0) {
      $result->_push(array_slice($this->_array, $start, $end));
    } elseif ($end > $start) {
      $result->_push(array_slice($this->_array, $start, $end - $start));
    } else {
      $result->_push(array_slice($this->_array, $end, $start - $end));
    }
    return $result;
  }

  /*
  * Traversing - Finding
  */

  /**
  * Adds more elements, matched by the given expression, to the set of matched elements.
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  public function add($expr) {
    $result = $this->_spawn();
    $result->_push($this->_array);
    if (is_object($expr)) {
      $result->_push($expr);
    } elseif (isset($this->_parent)) {
      $result->_push($this->_parent->find($expr));
    } else {
      $result->_push($this->find($expr));
    }
    return $result;
  }

  /**
  * Get a set of elements containing all of the unique immediate
  * children of each of the matched set of elements.
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  function children($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      if (empty($expr)) {
        $result->_push($node->childNodes, TRUE);
      } else {
        foreach ($node->childNodes as $childNode) {
          if ($this->_test($expr, $childNode)) {
            $result->_push($next, TRUE);
          }
        }
      }
    }
    return $result;
  }

  /**
  * Searches for descendent elements that match the specified expression.
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  public function find($expr) {
    $result = $this->_spawn();
    if ($this->_useDocumentContext) {
      $result->_push($this->_match($expr));
    } else {
      foreach ($this->_array as $contextNode) {
        $result->_push($this->_match($expr, $contextNode));
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing the unique next siblings of each of the given set of elements.
  *
  * Like jQuerys next() method but renamed because of a conflict with Iterator
  *
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  function nextSiblings($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      $next = $node->nextSibling;
      while ($next instanceof DOMNode && !$this->_isNode($next)) {
        $next = $next->nextSibling;
      }
      if (!empty($next)) {
        if (empty($expr) || $this->_test($expr, $next)) {
          $result->_push($next, TRUE);
        }
      }
    }
    return $result;
  }

  /**
  * Find all sibling elements after the current element.
  *
  * Like jQuerys nextAll() method but renamed for consistency with nextSiblings()
  *
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  function nextAllSiblings($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      $next = $node->nextSibling;
      while ($next instanceof DOMNode) {
        if ($this->_isNode($next)) {
          if (empty($expr) || $this->_test($expr, $next)) {
            $result->_push($next, TRUE);
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
  * @access public
  * @return FluentDOM
  */
  function parent() {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
        $result->_push($node->parentNode, TRUE);
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing the unique ancestors of the matched set of elements.
  *
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  function parents($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      $parents = $this->_match('ancestor::*', $node);
      for ($i = $parents->length - 1; $i >= 0; --$i) {
        $parentNode = $parents->item($i);
        if (empty($expr) || $this->_test($expr, $parentNode)) {
          $result->_push($parentNode, TRUE);
        }
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing the unique previous siblings of each of the matched set of elements.
  *
  * Like jQuerys prev() method but renamed for consistency with nextSiblings()
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  function prevSiblings($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      $previous = $node->previousSibling;
      while ($previous instanceof DOMNode && !$this->isNode($previous)) {
        $previous = $previous->previousSibling;
      }
      if (!empty($previous)) {
        if (empty($expr) || $this->_test($expr, $previous)) {
          $result->_push($previous, TRUE);
        }
      }
    }
    return $result;
  }

  /**
  * Find all sibling elements in front of the current element.
  *
  * Like jQuerys prevAll() method but renamed for consistency with nextSiblings()
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  function prevAllSiblings($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      $previous = $node->previousSibling;
      while ($previous instanceof DOMNode) {
        if ($this->_isNode($previous)) {
          if (empty($expr) || $this->_test($expr, $previous)) {
            $result->_push($previous, TRUE);
          }
        }
        $previous = $previous->previousSibling;
      }
    }
    return $result;
  }

  /**
  * Get a set of elements containing all of the unique siblings of each of the matched set of elements.
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  function siblings($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
        $siblings = $node->parentNode->childNodes;
        foreach ($node->parentNode->childNodes as $childNode) {
          if ($this->_isNode($childNode) &&
              $childNode !== $node) {
            if (empty($expr) || $this->_test($expr, $childNode)) {
              $result->_push($childNode, TRUE);
            }
          }
        }
      }
    }
    return $result;
  }

  /*
  * Traversing - Chaining
  */

  /**
  * Add the previous selection to the current selection.
  *
  * @access public
  * @return object FluentDOM
  */
  public function andSelf() {
    $result = $this->_spawn();
    $result->_push($this->_array);
    $result->_push($this->_parent);
    return $result;
  }

  /**
  * Revert the most recent traversing operation,
  * changing the set of matched elements to its previous state.
  *
  * @access public
  * @return object FluentDOM
  */
  public function end() {
    if (!empty($this->_parent)) {
      return $this->_parent;
    } else {
      return $this;
    }
  }

  /*
  * Manipulation - Changing Contents
  */

  /**
  * Get or set the xml contents of the first matched element.
  *
  * @param string $xml XML fragment
  * @access public
  * @return string | object FluentDOM
  */
  public function xml($xml = NULL) {
    if (isset($xml)) {
      if (!empty($xml)) {
        $fragment = $this->_document->createDocumentFragment();
        if ($fragment->appendXML($xml)) {
          foreach ($this->_array as $node) {
            $node->nodeValue = '';
            $node->appendChild($fragment->cloneNode(TRUE));
          }
        }
      }
      return $this;
    } else {
      $result = '';
      if (isset($this->_array[0])) {
        foreach ($this->_array[0]->childNodes as $childNode) {
          if ($this->_isNode($childNode)) {
            $result .= $this->_document->saveXML($childNode);
          }
        }
      }
      return $result;
    }
  }

  /**
  * Get the combined text contents of all matched elements or
  * set the text contents of all matched elements.
  *
  * @param string $text
  * @access public
  * @return string | object FluentDOM
  */
  public function text($text = NULL) {
    if (isset($text)) {
      foreach ($this->_array as $node) {
        $node->nodeValue = $text;
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
  * @param string | object DOMNode | object FluentDOM $content DOMNode or DOMNodeList or xml fragment string
  * @access public
  * @return string | object FluentDOM
  */
  public function append($content) {
    return $this->_insertChild($content, FALSE);
  }

  /**
  * Append all of the matched elements to another, specified, set of elements.
  * Returns all of the inserted elements.
  *
  * @param string | object DOMElement | object FluentDOM $expr XPath expression, element or list of elements
  * @access public
  * @return object FluentDOM
  */
  public function appendTo($expr) {
    return $this->_insertChildTo($expr, FALSE);
  }

  /**
  * Prepend content to the inside of every matched element.
  *
  * @param string | object DOMNode | object FluentDOM $content DOMNode or DOMNodeList or xml fragment string
  * @access public
  * @return string | object FluentDOM
  */
  public function prepend($content) {
    return $this->_insertChild($content, TRUE);
  }

  /**
  * Prepend all of the matched elements to another, specified, set of elements.
  * Returns all of the inserted elements.
  *
  * @param string | object DOMElement | object FluentDOM $expr XPath expression, element or list of elements
  * @access public
  * @return object FluentDOM list of all new elements
  */
  public function prependTo($expr) {
    return $this->_insertChildTo($expr, TRUE);
  }

  /**
  * Insert content to the inside of every matched element.
  *
  * @param string | object DOMNode | object FluentDOM $content DOMNode or DOMNodeList or xml fragment string
  * @param boolean $first insert at first position (or last)
  * @access private
  * @return object FluentDOM
  */
  private function _insertChild($content, $first) {
    if (!empty($content)) {
      if ($content instanceof DOMNode) {
        foreach ($this->_array as $node) {
          $node->insertBefore(
            $content->cloneNode(TRUE),
            ($first && $node->hasChildNodes()) ? $node->childNodes->item(0) : NULL
          );
        }
      } elseif ($content instanceof FluentDOM) {
        foreach ($this->_array as $node) {
          foreach ($content as $contentNode) {
            $node->insertBefore(
              $contentNode->cloneNode(TRUE),
              ($first && $node->hasChildNodes()) ? $node->childNodes->item(0) : NULL
            );
          }
        }
      } else {
        $fragment = $this->_document->createDocumentFragment();
        if ($fragment->appendXML($content)) {
          foreach ($this->_array as $node) {
            $node->insertBefore(
              $fragment->cloneNode(TRUE),
              ($first && $node->hasChildNodes()) ? $node->childNodes->item(0) : NULL
            );
          }
        }
      }
    }
    return $this;
  }

  /**
  * Insert all of the matched elements to another, specified, set of elements.
  * Returns all of the inserted elements.
  *
  * @param string | object DOMElement | object FluentDOM $selector XPath expression, element or list of elements
  * @param boolean $first insert at first position (or last)
  * @access public
  * @return object FluentDOM
  */
  private function _insertChildTo($selector, $first) {
    $result = $this->_spawn();
    $targets = $this->_getTargetNodes($selector);
    if (!empty($targets)) {
      foreach ($targets as $targetNode) {
        if ($targetNode instanceof DOMElement) {
          foreach ($this->_array as $node) {
            $result->_push(
              $targetNode->insertBefore(
                $node->cloneNode(TRUE),
                ($first && $targetNode->hasChildNodes())
                  ? $targetNode->childNodes->item(0) : NULL
              )
            );
          }
        }
        $this->_removeNodes($this->_array);
      }
    }
    return $result;
  }

  /*
  * Manipulation - Inserting Outside
  */

  /**
  * Insert content after each of the matched elements.
  *
  * @param $content
  * @access public
  * @return
  */
  public function after($content) {
    $result = $this->_spawn();
    if ($contentNodes = $this->_getContentNodes($content, TRUE)) {
      foreach ($this->_array as $node) {
        $beforeNode = $node->nextSibling;
        if (isset($node->parentNode)) {
          foreach ($contentNodes as $contentNode) {
            $result->_push(
              $node->parentNode->insertBefore(
                $contentNode->cloneNode(TRUE),
                $beforeNode
              )
            );
          }
        }
      }
    }
    return $result;
  }
  
  /**
  * Insert content before each of the matched elements.
  *
  * @param $content
  * @access public
  * @return object FluentDOM 
  */
  public function before($content) {
    $result = $this->_spawn();
    if ($contentNodes = $this->_getContentNodes($content, TRUE)) {
      foreach ($this->_array as $node) {
        if (isset($node->parentNode)) {
          foreach ($contentNodes as $contentNode) {
            $result->_push(
              $node->parentNode->insertBefore(
                $contentNode->cloneNode(TRUE),
                $node
              )
            );
          }
        }
      }
    }
    return $result;
  }
  
  /**
  * Insert all of the matched elements after another, specified, set of elements.
  *
  * @param $selector
  * @access public
  * @return object FluentDOM
  */
  public function insertAfter($selector) {
    $result = $this->_spawn();
    $targets = $this->_getTargetNodes($selector);
    if (!empty($targets)) {
      foreach ($targets as $targetNode) {
        if ($this->_isNode($targetNode) && isset($targetNode->parentNode)) {
          $beforeNode = $targetNode->nextSibling;
          foreach ($this->_array as $node) {
            $result->_push(
              $targetNode->parentNode->insertBefore(
                $node->cloneNode(TRUE),
                $beforeNode
              )
            );
          }
        }
        $this->_removeNodes($this->_array);
      }
    }
    return $result;
  }
  
  /**
  * Insert all of the matched elements before another, specified, set of elements.
  *
  * @param $selector
  * @access public
  * @return object FluentDOM
  */
  public function insertBefore($selector) {
    $result = $this->_spawn();
    $targets = $this->_getTargetNodes($selector);
    if (!empty($targets)) {
      foreach ($targets as $targetNode) {
        if ($this->_isNode($targetNode) && isset($targetNode->parentNode)) {
          foreach ($this->_array as $node) {
            $result->_push(
              $targetNode->parentNode->insertBefore(
                $node->cloneNode(TRUE),
                $targetNode
              )
            );
          }
        }
        $this->_removeNodes($this->_array);
      }
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
  * @param string | array | object DOMElement | object FluentDOM $content
  * @access private
  * @return object FluentDOM
  */
  private function _wrap($elements, $content) {
    $wrapperTemplate = $this->_getContentElement($content);
    $result = array();
    if ($wrapperTemplate instanceof DOMElement) {
      $simple = FALSE;
      foreach ($elements as $node) {
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
  * @param string | array | object DOMElement | object FluentDOM $content
  * @access public
  * @return object FluentDOM
  */
  public function wrap($content) {
    $result = $this->_spawn();
    $result->_push($this->_wrap($this->_array, $content));
    return $result;
  }
  
  /**
  * Wrap al matched elements with the specified content
  *
  * If the matched elemetns are not siblings, wrap each group of siblings.
  *
  * @param string | array | object DOMElement | object FluentDOM $content
  * @access public
  * @return object FluentDOM
  */
  public function wrapAll($content) {
    $result = $this->_spawn();
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
          $result->_push($node);
        }
      }
    }
    return $result;
  }
  
  /**
  * Wrap the inner child contents of each matched element
  * (including text nodes) with an XML structure.
  *
  * @param string | array | object DOMElement | object FluentDOM $content
  * @access public
  * @return FluentDOM
  */
  public function wrapInner($content) {
    $result = $this->_spawn();
    $elements = array();
    foreach ($this->_array as $node) {
      foreach ($node->childNodes as $childNode) {
        if ($this->_isNode($childNode)) {
          $elements[] = $childNode;   
        }
      }
    }
    $result->_push($this->_wrap($elements, $content));
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
  * @param $content
  * @access public
  * @return
  */
  public function replaceWith($content) {
    $contentNodes = $this->_getContentNodes($content);
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
        foreach ($contentNodes as $contentNode) {
          $node->parentNode->insertBefore(
            $contentNode->cloneNode(TRUE),
            $node
          );
        }
      }
    }
    $this->_removeNodes($this->_array);
    return $this;
  }
  
  /**
  * Replaces the elements matched by the specified selector with the matched elements.
  *
  * @param $selector
  * @access public
  * @return object FluentDOM
  */
  public function replaceAll($selector) {
    $result = $this->_spawn();
    $targetNodes = $this->_getTargetNodes($selector);
    foreach ($targetNodes as $targetNode) {
      if (isset($targetNode->parentNode)) {
        foreach ($this->_array as $node) {
          $result->_push(
            $targetNode->parentNode->insertBefore(
              $node->cloneNode(TRUE),
              $targetNode
            )
          );
        }
      }
    }
    $this->_removeNodes($targetNodes);
    $this->_removeNodes($this->_array);
    return $result;
  }
  
  /*
  * Manipulation - Removing
  */
  
  /**
  * this is the empty() method - but because empty
  * is a reserved word we can no declare it directly
  * @see __call
  *
  * @access private
  * @return object FluentDOM
  */
  private function _emptyNodes() {
    foreach ($this->_array as $node) {
      if ($node instanceof DOMElement) {
        $node->nodeValue = '';
      } elseif ($node instanceof DOMText) {
        $node->textContent = '';
      }
    }
    return $this;
  }
  
  /**
  * Removes all matched elements from the DOM.
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM removed elements
  */
  public function remove($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
        if (empty($expr) || $this->test($expr, $node)) {
          $result->_push($node->parentNode->removeChild($node));
        }
      }  
    }
    return $result;
  }

  /*
  * Manipulation - Creation
  */
  
  /**
  * create nodes list from content, if $content contains node(s)
  * from another document the are imported. 
  *
  * @param $content
  * @access public
  * @return object FluentDOM
  */
  public function node($content) {
    $result = $this->_spawn();
    $result->_push($this->_getContentNodes($content));
    return $result;
  }

  /*
  * Manipulation - Copying
  */

  /**
  * Clone matched DOM Elements and select the clones.
  *
  * @access private
  * @return object FluentDOM
  */
  private function _cloneNodes() {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      $result->_push($node->cloneNode(TRUE));
    }
    return $result;
  }
  
  /*
  * Attributes - General
  */

  /**
  * Access a property on the first matched element or set the attribute(s) of all matched elements
  *
  * @param string | array $expr attribute name or attribute list
  * @param callback | string $value function callback or value
  * @access public
  * @return string | object FluentDOM attribute value or $this
  */
  public function attr($expr, $value = NULL) {
    if (is_array($expr) && count($expr)) {
      //expr is an array of attributes and values - set on each element
      foreach ($expr as $key => $value) {
        if ($this->_isQName($key)) {
          foreach ($this->_array as $node) {
            if ($node instanceof DOMElement) {
              $node->setAttribute($key, $value);
            }
          }
        }
      }
    } elseif (empty($value)) {
      //empty value - read attribute from first element in list
      if ($this->_isQName($expr) && isset($this->_array[0])) {
        return $this->_array[0]->getAttribute($expr);
      } else {
        return NULL;
      }
    } elseif (is_array($value)) {
      //value is an array (function callback) - execute ist and set result on each element
      if ($this->_isQName($expr)) {
        foreach ($this->_array as $node) {
          if ($node instanceof DOMElement) {
            $node->setAttribute($expr, call_user_func($value, $node));
          }
        }
      }
    } else {
      // set attribute value of each element
      if ($this->_isQName($expr)) {
        foreach ($this->_array as $node) {
          if ($node instanceof DOMElement) {
            $node->setAttribute($expr, $value);
          }
        }
      }
    }
    return $this;
  }

  /**
  * Remove an attribute from each of the matched elements.
  *
  * @param string $name
  * @access public
  * @return object FluentDOM
  */
  public function removeAttr($name) {
    if (!empty($name)) {
      foreach ($this->_array as $node) {
        if ($node instanceof DOMElement &&
            $node->hasAttribute($name)) {
          $node->removeAttribute($name);
        }
      }
    }
    return $this;
  }

  /*
  * Attributes - Classes
  */

  /**
  * Adds the specified class(es) to each of the set of matched elements.
  *
  * @param string $class
  * @access public
  * @return object FluentDOM
  */
  public function addClass($class) {
    return $this->toggleClass($class, TRUE);
  }

  /**
  * Returns true if the specified class is present on at least one of the set of matched elements.
  *
  * @param string $class
  * @access public
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
  * @param $class
  * @access public
  * @return object FluentDOM
  */
  public function removeClass($class) {
    return $this->toggleClass($class, FALSE);
  }

  /**
  * Adds the specified class if the switch is TRUE,
  * removes the specified class if the switch is FALSE,
  * toggles the specified class if the switch is NULL.
  *
  * @param string $class
  * @param NULL | boolean $switch toggle if NULL, add if TRUE, remove if FALSE
  * @access public
  * @return object FluentDOM
  */
  public function toggleClass($class, $switch = NULL) {
    foreach ($this->_array as $node) {
      if ($node instanceof DOMElement) {
        if ($node->hasAttribute('class')) {
          $currentClasses = array_flip(
            preg_split('(\s+)',
            trim($node->getAttribute('class')))
          );
        } else {
          $currentClasses = array();
        }
        $toggledClasses = array_unique(preg_split('(\s+)', trim($class)));
        $modified = FALSE;
        foreach($toggledClasses as $toggledClass) {
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
    return $this;
  }
}
?>