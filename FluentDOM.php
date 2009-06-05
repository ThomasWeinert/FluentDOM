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
  * @var
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
  * @var
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
    } elseif ($source instanceof DOMElement) {
      $this->_document = $source->ownerDocument;
      $this->push($source);
    } elseif (is_string($source)) {
      $this->_document = new DOMDocument();
      $this->_document->loadXML($source);
      $this->_useDocumentContext = TRUE;
    } else {
      throw new Exception('Invalid source object');
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
      return $this->xpath();
    default :
      return NULL;
    }
  }

  /**
  * block changes of dynmaic readonly property length
  *
  * @param $name
  * @param $value
  * @access public
  * @return void
  */
  public function __set($name, $value) {
    if ($name != 'length' && $name != 'document' && $name != 'xpath') {
      $this->$name = $value;
    }
  }

  /**
  * support isst for dynic properties length and document
  *
  * @param $name
  * @access public
  * @return
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
  * @return
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
      throw new Exception('Unknown Index');
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
    $result = new FluentDOM($this);
    $result->push($this->match('*', $this->_array[$this->_position]));
    return $result;
  }
  
  /**
  * Check if the current iterator element has children
  *
  * @access public
  * @return object FluentDOM
  */
  public function hasChildren() {
    return $this->test('count(*)', $this->_array[$this->_position]);
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
    throw new Exception('List is read only');
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
    throw new Exception('List is read only');
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
  * Core functions for node handling
  */

  /**
  * create a new xpath object an register default namespaces from the current document
  *
  * @access private
  * @return object DOMXPath
  */
  private function xpath() {
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
  private function match($expr, $context = NULL) {
    if (isset($context)) {
      return $this->xpath()->query($expr, $context);
    } else {
      return $this->xpath()->query($expr);
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
  private function test($expr, $context = NULL) {
    if (isset($context)) {
      $check = $this->xpath()->evaluate($expr, $context);
    } else {
      $check = $this->xpath()->evaluate($expr);
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
  private function push($elements, $unique = FALSE) {
    if ($elements instanceof DOMElement) {
      if ($elements->ownerDocument == $this->_document) {
        if (!$unique || !$this->inList($elements, $this->_array)) {
          $this->_array[] = $elements;
        }
      } else {
        throw new Exception('DOMElement is not a part of this DOMDocument');
      }
    } elseif ($elements instanceof DOMNodeList ||
              $elements instanceof DOMDocumentFragment ||
              $elements instanceof Iterator ||
              is_array($elements)) {
      foreach ($elements as $node) {
        if ($node instanceof DOMElement) {
          if ($node->ownerDocument == $this->_document) {
            if (!$unique || !$this->inList($node, $this->_array)) {
              $this->_array[] = $node;
            }
          } else {
            throw new Exception('DOMElement is not a part of this DOMDocument');
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
  private function inList($node) {
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
  private function isQName($name) {
    return TRUE;
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
      throw new Exception('Invalid callback function');
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
    $result = new FluentDOM($this);
    if (isset($this->_array[$position])) {
      $result->push($this->_array[$position]);
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
    $result = new FluentDOM($this);
    foreach ($this->_array as $index => $node) {
      if (is_string($expr)) {
        $check = $this->test($expr, $node);
      } elseif ($expr instanceof Closure ||
                is_array($expr)) {
        $check = call_user_func($expr, $node, $index);
      } else {
        $check = TRUE;
      }
      if ($check) {
        $result->push($node);
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
      return $this->test($expr, $node);
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
        throw new Exception('Invalid callback function');
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
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  public function not($expr) {
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      if ($this->test($expr, $node)) {
        $result->push($node);
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
    $result = new FluentDOM($this);
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
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  public function add($expr) {
    $result = new FluentDOM($this);
    $result->push($this->_array);
    if (is_object($expr)) {
      $result->push($expr);
    } elseif (isset($this->_parent)) {
      $result->push($this->_parent->find($expr));
    } else {
      $result->push($this->find($expr));
    }
    return $result;
  }

  /**
  * Get a set of elements containing all of the unique immediate children of each of the matched set of elements.
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOM
  */
  function children($expr = NULL) {
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      if (empty($expr)) {
        $result->push($node->childNodes, TRUE);
      } else {
        foreach ($node->childNodes as $childNode) {
          if ($this->test($expr, $childNode)) {
            $result->push($next, TRUE);
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
    $result = new FluentDOM($this);
    if ($this->_useDocumentContext) {
      $result->push($this->match($expr));
    } else {
      foreach ($this->_array as $contextNode) {
        $result->push($this->match($expr, $contextNode));
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
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      $next = $node->nextSibling;
      while ($next instanceof DOMNode && !($next instanceof DOMElement)) {
        $next = $next->nextSibling;
      }
      if (!empty($next)) {
        if (empty($expr) || $this->test($expr, $next)) {
          $result->push($next, TRUE);
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
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      $next = $node->nextSibling;
      while ($next instanceof DOMNode) {
        if ($next instanceof DOMElement) {
          if (empty($expr) || $this->test($expr, $next)) {
            $result->push($next, TRUE);
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
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      $result->push($node->parentNode, TRUE);
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
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      $parents = $this->match('ancestor::*', $node);
      for ($i = $parents->length - 1; $i >= 0; --$i) {
        $parentNode = $parents->item($i);
        if (empty($expr) || $this->test($expr, $parentNode)) {
          $result->push($parentNode, TRUE);
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
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      $next = $node->previousSibling;
      while ($next instanceof DOMNode && !($next instanceof DOMElement)) {
        $next = $next->previousSibling;
      }
      if (!empty($next)) {
        if (empty($expr) || $this->test($expr, $next)) {
          $result->push($next, TRUE);
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
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      $next = $node->previousSibling;
      while ($next instanceof DOMNode) {
        if ($next instanceof DOMElement) {
          if (empty($expr) || $this->test($expr, $next)) {
            $result->push($next, TRUE);
          }
        }
        $next = $next->previousSibling;
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
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      $siblings = $node->parentNode->childNodes;
      foreach ($node->parentNode->childNodes as $childNode) {
        if ($childNode instanceof DOMElement &&
            $childNode !== $node) {
          if (empty($expr) || $this->test($expr, $childNode)) {
            $result->push($childNode, TRUE);
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
    $result = new FluentDOM($this);
    $result->push($this->_array);
    $result->push($this->_parent);
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
          $result .= $this->_document->saveXML($childNode);
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
    return $this->insertChild($content, FALSE);
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
    return $this->insertChildTo($expr, FALSE);
  }

  /**
  * Prepend content to the inside of every matched element.
  *
  * @param string | object DOMNode | object FluentDOM $content DOMNode or DOMNodeList or xml fragment string
  * @access public
  * @return string | object FluentDOM
  */
  public function prepend($content) {
    return $this->insertChild($content, TRUE);
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
    return $this->insertChildTo($expr, TRUE);
  }

  /**
  * Insert content to the inside of every matched element.
  *
  * @param string | object DOMNode | object FluentDOM $content DOMNode or DOMNodeList or xml fragment string
  * @param boolean $first insert at first position (or last)
  * @access private
  * @return object FluentDOM
  */
  private function insertChild($content, $first) {
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
  * @param string | object DOMElement | object FluentDOM $expr XPath expression, element or list of elements
  * @param boolean $first insert at first position (or last)
  * @access public
  * @return object FluentDOM
  */
  public function insertChildTo($expr, $first) {
    $result = new FluentDOM($this->_document, $this);
    if (!empty($expr)) {
      if ($expr instanceof DOMElement) {
        foreach ($this->_array as $node) {
          $result->push(
            $expr->insertBefore(
              $node->cloneNode(TRUE),
              ($first && $expr->hasChildNodes()) ? $expr->childNodes->item(0) : NULL
            )
          );
        }
        $node->parentNode->removeChild($node);
      } elseif ($expr instanceof FluentDOM) {
        foreach ($expr as $exprNode) {
          foreach ($this->_array as $node) {
            $result->push(
              $exprNode->insertBefore(
                $node->cloneNode(TRUE),
                ($first && $exprNode->hasChildNodes()) ? $exprNode->childNodes->item(0) : NULL
              )
            );
          }
        }
        foreach ($this->_array as $node) {
          $node->parentNode->removeChild($node);
        }
      } elseif (is_string($expr)) {
        $targets = $this->match($expr);
        foreach ($targets as $exprNode) {
          foreach ($this->_array as $node) {
            $result->push(
              $exprNode->insertBefore(
                $node->cloneNode(TRUE),
                ($first && $exprNode->hasChildNodes()) ? $exprNode->childNodes->item(0) : NULL
              )
            );
          }
        }
        foreach ($this->_array as $node) {
          $node->parentNode->removeChild($node);
        }
      }
    }
    return $result;
  }

  /*
  * Manipulation - Inserting Inside
  */

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
    $wrapperTemplate = $this->_getWrapper($content);
    if ($wrapperTemplate instanceof DOMElement) {
      $simple = FALSE;
      foreach ($elements as $node) {
        $wrapper = $wrapperTemplate->cloneNode(TRUE);
        if (!$simple) {
          $targets = $this->match('.//*[count(*) = 0]', $wrapper);
        }
        if ($simple || $targets->length == 0) {
          $target = $wrapper;
          $simple = TRUE;
        } else {
          $target = $targets->item(0);
        }
        $node->parentNode->insertBefore($wrapper, $node);
        $target->appendChild($node);
      }
    } else {
       
    }
    return $this;
  }
  
  /**
  * Convert wrapper content to DOMElement
  *
  * @param string | array | object DOMElement | object FluentDOM $content
  * @access private
  * @return object DOMElement
  */
  private function _getWrapper($content) {
    if ($content instanceof DOMElement) {
      return $content;
    } elseif (is_string($content)) {
      $fragment = $this->_document->createDocumentFragment();
      if ($fragment->appendXML($content)) {
        foreach ($fragment->childNodes as $element) {
          if ($element instanceof DOMElement) {
            $element->parentNode->removeChild($element);
            return $element;
          }
        }
      } else {
        throw new Exception('Invalid document fragment');
      }
    } elseif ($content instanceof DOMNodeList ||
              $content instanceof Iterator ||
              is_array($content)) {
      foreach ($content as $element) {
        if ($element instanceof DOMElement) {
          return $element;  
        } 
      }
    }
    throw new Exception('No element found'); 
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
    return $this->_wrap($this->_array, $content);
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
    $current = NULL;
    $counter = 0;
    $groups = array();
    //group elements by previous node - ignore whitespace text nodes
    foreach ($this->_array as $node) {
      $previous = $node->previousSibling;
      while ($previous instanceof DOMText && trim($previous->textContent) == '') {
        $previous = $previous->previousSibling;
      }
      if ($previous !== $current) {
        $counter++;
      }
      $groups[$counter][] = $node;
      $current = $node;
    }
    if (count($groups) > 0) {
      $wrapperTemplate = $this->_getWrapper($content);
      $simple = FALSE;
      foreach ($groups as $group) {
        if (isset($group[0])) {
          $node = $group[0];
          $wrapper = $wrapperTemplate->cloneNode(TRUE);
          if (!$simple) {
            $targets = $this->match('.//*[count(*) = 0]', $wrapper);
          }
          if ($simple || $targets->length == 0) {
            $target = $wrapper;
            $simple = TRUE;
          } else {
            $target = $targets->item(0);
          }
          $node->parentNode->insertBefore($wrapper, $node);
          foreach ($group as $node) {
            $target->appendChild($node);
          }
        }
      }
    }
    return $this;
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
    $elements = array();
    foreach ($this->_array as $node) {
      foreach ($node->childNodes as $childNode) {
        if ($childNode instanceof DOMElement ||
            $childNode instanceof DOMText) {
          $elements[] = $childNode;   
        }
      }
    }
    return $this->_wrap($elements, $content);
  }
  
  /*
  * Manipulation - Replacing
  */

  /*
  * Manipulation - Removing
  */
  
  /**
  * this is the empty() method - but because empty
  * is a reserved word we can no declare it directly
  * @see __call
  *
  * @access public
  * @return object FluentDOM
  */
  private function _emptyNodes() {
    foreach ($this->_array as $node) {
      $node->nodeValue = '';
    }
    return $this;
  }

  /*
  * Manipulation - Copying
  */

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
        if ($this->isQName($key)) {
          foreach ($this->_array as $node) {
            $node->setAttribute($key, $value);
          }
        }
      }
    } elseif (empty($value)) {
      //empty value - read attribute from first element in list
      if ($this->isQName($expr) && isset($this->_array[0])) {
        return $this->_array[0]->getAttribute($expr);
      } else {
        return NULL;
      }
    } elseif (is_array($value)) {
      //value is an array (function callback) - execute ist and set result on each element
      if ($this->isQName($expr)) {
        foreach ($this->_array as $node) {
          $node->setAttribute($expr, call_user_func($value, $node));
        }
      }
    } else {
      // set attribute value of each element
      if ($this->isQName($expr)) {
        foreach ($this->_array as $node) {
          $node->setAttribute($expr, $value);
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
        if ($node->hasAttribute($name)) {
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
      if ($node->hasAttribute('class')) {
        $classes = preg_split('\s+', trim($node->getAttribute('class')));
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
      if ($node->hasAttribute('class')) {
        $currentClasses = array_flip(preg_split('(\s+)', trim($node->getAttribute('class'))));
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
    return $this;
  }
}
?>