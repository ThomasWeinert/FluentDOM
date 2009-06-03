<?php
/**
* FluentDOM implements a jQuery like replacement for DOMNodeList
* 
* @version $Id: FluentDOM.php,v 0.0 00.00.0000 00:00:00 weinert Exp $
*/


/**
* function to create a new FluentDOM instance
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
class FluentDOM implements Iterator, Countable {

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
    } elseif ($source instanceof DOMELement) {
      $this->_document = $source->ownerDocument;
      $this->push($source);
    } elseif (is_string($source)) {
      $this->_document = new DOMDocument();
      $this->_document->loadXML($source);
      $this->_useDocumentContext = TRUE;
    } else {
      throw new Exception('Invalid document object');
    }
  }
  
  /**
  * create a new xpath object an register all namespaces from the current document
  *
  * @access private
  * @return object DOMXPath
  */
  private function xpath() {
    if (empty($this->_xpath) || $this->_xpath->document != $this->_document) {
      $this->_xpath = new DOMXPath($this->_document);
      foreach ($this->_xpath->query('namespace::*') as $namespace) {
        if ($namespace->localName == 'xmlns') {
          $this->_xpath->registerNamespace('_', $namespace->namespaceURI);
        } else {
          $this->_xpath->registerNamespace($namespace->localName, $namespace->namespaceURI);
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
  * implement dynamic property length using magic methods  
  *
  * @param string $name 
  * @access public
  * @return mixed
  */
  public function __get($name) {
    switch ($name) {
    case 'length' : 
      return count($this->_array);
      break;
    case 'document' :
      return $this->_document;
      break;
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
    if ($name != 'length' && $name != 'document') {
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
      return TRUE;
    case 'document' :
      return isset($this->_document);
    }
    return FALSE;
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
  
  /**
  * reset iterator pointer (Iterator)
  *
  * @access public
  * @return void
  */
  public function rewind() {
    $this->_position = 0;
  }

  /**
  * get current element (Iterator)
  *
  * @access public
  * @return DOMNode 
  */
  public function current() {
    return $this->_array[$this->_position];
  }

  /**
  * get current key (Iterator)
  *
  * @access public
  * @return integer
  */
  public function key() {
    return $this->_position;
  }

  /**
  * move key to next element (Iterator)
  *
  * @access public
  * @return
  */
  public function next() {
    ++$this->_position;
  }

  /**
  * check if current position contains a valid element (Iterator)
  *
  * @access public
  * @return boolean
  */
  public function valid() {
    return isset($this->_array[$this->_position]);
  }
  
  /**
  * get element count (Countable)
  *
  * @access public
  * @return
  */
  public function count() {
    return count($this->_array);
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
  * Traversing: add elements to list
  *
  * @param $expr
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
  * Traversing: push parent elements to current list - return merged list.
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
  * Traversing: return parent list or document
  *
  * @access public
  * @return object FluentDOM |  object DOMDocument
  */
  public function end() {
    if (!empty($this->_parent)) {
      return $this->_parent;
    } else {
      return $this;
    }
  }
  
  /**
  * Traversing: return a new list with one element defined by position
  *
  * @param integer $position
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
  * Traversing: return new list with selected elements
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
  * put all nodes matching expression in a new list
  *
  * @param string $expr XPath expression
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
  * chek if one of the nodes in the list matchers the expression
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
  * return a new list containing all elements from the current
  * list that do not match the expression
  *
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
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
  
  /**
  * attribute manipulation and reading
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
  * remove attribute from all elements in list
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
  
  /**
  * add css class to all elements in list
  *
  * @param string $class
  * @access public
  * @return object FluentDOM
  */
  public function addClass($class) {
    return $this->toggleClass($class, TRUE);
  }
  
  /**
  * check if one element in node list has the specified css class
  *
  * @param string $class
  * @access public
  * @return object FluentDOM
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
  * remove css class to all elements in list
  *
  * @param $class
  * @access public
  * @return object FluentDOM
  */
  public function removeClass($class) {
    return $this->toggleClass($class, FALSE);
  }
  
  /**
  * remove css class to all elements in list
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
  
  /**
  * get xml content of first element or set xml content of all elements in list
  *
  * @param string $xml optional, default value NULL
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
  * get text content of first element or set text content of all elements in list
  *
  * @param string $text optional, default value NULL
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
  
  /**
  * append clones of elements defined by $expr to all elements in list
  *
  * @param string | object DOMNode | object FluentDOM $expr DOMNode or DOMNodeList or xml fragment string
  * @access public
  * @return string | object FluentDOM
  */
  public function append($expr) {
    return $this->insertChild($expr, FALSE);
  }
  
  /**
  * preppend clones of elements defined by $expr to all elements in list
  *
  * @param $expr
  * @access public
  * @return
  */
  public function prepend($expr) {
    return $this->insertChild($expr, TRUE);
  }
  
  /**
  * insert clones of elements defined by $expr before or after the children of all elements in the list
  *
  * @param string | object DOMNode | object FluentDOM $expr DOMNode or DOMNodeList or xml fragment string
  * @param boolean $first insert at first position (or last)
  * @access private
  * @return object FluentDOM
  */
  private function insertChild($expr, $first) {
    if (!empty($expr)) {
      if ($expr instanceof DOMNode) {
        foreach ($this->_array as $node) {
          $node->insertBefore(
            $expr->cloneNode(TRUE),
            ($first && $node->hasChildNodes()) ? $node->childNodes->item(0) : NULL
          );
        }
      } elseif ($expr instanceof FluentDOM) {
        foreach ($this->_array as $node) {
          foreach ($expr as $exprNode) {
            $node->insertBefore(
              $exprNode->cloneNode(TRUE),
              ($first && $node->hasChildNodes()) ? $node->childNodes->item(0) : NULL
            );
          }
        }
      } else {
        $fragment = $this->_document->createDocumentFragment();
        if ($fragment->appendXML($expr)) {
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
  * append clones of elements in the list to child nodes of all elements defined by $expr
  *
  * @param string | object DOMElement | object FluentDOM $expr XPath expression, element or list of elements
  * @access public
  * @return object FluentDOM list of all new elements
  */
  public function appendTo($expr) {
    return $this->insertChildTo($expr, FALSE);
  }
  
  /**
  * prepend clones of elements in the list to child nodes of all elements defined by $expr
  *
  * @param string | object DOMElement | object FluentDOM $expr XPath expression, element or list of elements
  * @access public
  * @return object FluentDOM list of all new elements
  */
  public function prependTo($expr) {
    return $this->insertChildTo($expr, TRUE);
  }
  
  /**
  * insert clones of elements in the list to child nodes of all elements defined by $expr
  *
  * @param string | object DOMElement | object FluentDOM $expr XPath expression, element or list of elements
  * @param boolean $first insert at first position (or last)
  * @access public
  * @return object FluentDOM list of all new elements
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
  * maps the elements into an array using a mapping function
  *
  * @param $function
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
  * return a list of the unique parents of the current elements in the list
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
  * return all parents of all current elemtns in list
  *
  * @param string $expr optional, default value NULL
  * @access public
  * @return FluentDOM
  */
  function parents($expr = NULL) {
    $result = new FluentDOM($this);
    foreach ($this->_array as $node) {
      $parents = $this->match('ancestor::*', $node);
      for($i = $parents->length - 1; $i >= 0; --$i) {
        $parentNode = $parents->item($i);
        if (empty($expr) || $this->test($expr, $parentNode)) {
          $result->push($parentNode, TRUE);
        }
      }
    }
    return $result;
  }
  
  /**
  * list with the next sibling (unique) of each element in current list
  *
  * Like jQuerys next() method but renamed because of a conflict with Iterator
  *
  * @param string $expr optional, default value NULL
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
  * list with all siblings (unique) of all elements in current list
  *
  * Like jQuerys nextAll() method but renamed for consistency with nextSiblings()
  *
  * @param string $expr optional, default value NULL
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
  * list with the previous sibling (unique) of each element in current list
  *
  * Like jQuerys next() method but renamed for consistency with nextSiblings
  *
  * @param string $expr optional, default value NULL
  * @access public
  * @return FluentDOM
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
}
?>