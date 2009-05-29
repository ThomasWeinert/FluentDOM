<?php
/**
* FluentDOMNodelist implements a jQuery like replacement for DOMNodeList
* 
* @version $Id: FluentDOMNodeList.php,v 0.0 00.00.0000 00:00:00 weinert Exp $
*/


/**
* FluentDOMNodelist implements a jQuery like replacement for DOMNodeList
*/
class FluentDOMNodeList implements Iterator, Countable {

  /**
  * document object
  * @var object FluentDOMDocument
  * @access private
  */
  private $document = NULL;
  
  /**
  * parent node list (last selection in chain)
  * @var object FluentDOMNodeList
  * @access private
  */
  private $parent = NULL;
  
  /**
  * current iterator position
  * @var integer
  * @access private
  */  
  private $position = 0;
  
  /**
  * element nodes
  * @var 
  * @access private
  */
  private $array = array();
  
  /**
  * initialize the FluentDOMNodeList instance and set private properties
  *
  * @param object FluentDOMDocument $document document object
  * @param object FluentDOMNodeList | NULL $parent parent node list (last selection in chain)
  * @access public
  */
  public function __construct($document, $parent = NULL) {
    if ($document instanceof FluentDOMDocument) {
      $this->document = $document;
      if (isset($parent) && $parent instanceof FluentDOMNodeList) {
        $this->parent = $parent;
      }
    } else {
      throw new Exception('Invalid document object');
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
    if ($name == 'length') {
      return count($this->array);
    } else {
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
    if ($name != 'length') {
      $this->$name = $value;
    }
  }
  
  /**
  * return true
  *
  * @param $name
  * @access public
  * @return
  */
  public function __isset($name) {
    if ($name == 'length') {
      return TRUE;
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
    if (isset($this->array[$position])) {
      return $this->array[$position];
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
    $this->position = 0;
  }

  /**
  * get current element (Iterator)
  *
  * @access public
  * @return DOMNode 
  */
  public function current() {
    return $this->array[$this->position];
  }

  /**
  * get current key (Iterator)
  *
  * @access public
  * @return integer
  */
  public function key() {
    return $this->position;
  }

  /**
  * move key to next element (Iterator)
  *
  * @access public
  * @return
  */
  public function next() {
    ++$this->position;
  }

  /**
  * check if current position contains a valid element (Iterator)
  *
  * @access public
  * @return boolean
  */
  public function valid() {
    return isset($this->array[$this->position]);
  }
  
  /**
  * get element count (Countable)
  *
  * @access public
  * @return
  */
  public function count() {
    return count($this->array);
  }
  
  /**
  * Traversing: add elements to list
  *
  * @param $expr
  * @access public
  * @return object FluentDOMNodeList
  */
  public function add($expr) {
    if (is_object($expr) && $expr instanceof DOMElement) {
      $this->array[] = $expr;
    } elseif (is_object($expr) && $expr instanceof FluentDOMNodeList) {
      foreach ($expr as $node) {
        $this->array[] = $node;
      }
    }
    return $this;
  }
  
  /**
  * Traversing: add parent elements to current list - return merged list.
  *
  * @access public
  * @return object FluentDOMNodeList
  */
  public function andSelf() {
    $result = new FluentDOMNodeList($this->document, $this);
    foreach ($this->array as $node) {
      $result->add($node);
    }
    if (is_object($this->parent) && $this->parent instanceof FluentDOMNodeList) {
      foreach ($this->parent as $node) {
        $result->add($node);
      }
    }
    return $result;
  }
  
  /**
  * Traversing: return parent list or document
  *
  * @access public
  * @return object FluentDOMNodeList |  object FluentDOMDocument
  */
  public function end() {
    if (!empty($this->parent)) {
      return $this->parent;
    } else {
      return $this->document;
    }
  }
  
  /**
  * Traversing: return a new list with one element defined by position
  *
  * @param integer $position
  * @access public
  * @return object FluentDOMNodeList
  */
  public function eq($position) {
    $result = new FluentDOMNodeList($this->document, $this);
    if (isset($this->array[$position])) {
      $result->add($this->array[$position]);
    }
    return $result;
  }
  
  /**
  * Traversing: return new list with selected elements
  *
  * @param string $expr XPath expression
  * @access public
  * @return object FluentDOMNodeList
  */
  public function find($expr) {
    return $this->document->find($expr, $this);
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
  * @return string | object FluentDOMNodeList attribute value or $this
  */
  public function attr($expr, $value = NULL) {
    if (is_array($expr) && count($expr)) {
      //expr is an array of attributes and values - set on each element
      foreach ($expr as $key => $value) {
        if ($this->isQName($key)) {
          foreach ($this->array as $node) {
            $node->setAttribute($key, $value);
          }
        }
      }
    } elseif (empty($value)) {
      //empty value - read attribute from first element in list
      if ($this->isQName($expr) && isset($this->array[0])) {
        return $this->array[0]->getAttribute($expr);
      }
    } elseif (is_array($value)) {
      //value is an array (function callback) - execute ist and set result on each element
      if ($this->isQName($expr)) {
        foreach ($this->array as $node) {
          $node->setAttribute($expr, call_user_func($value, $node));
        }
      }
    } else {
      // set attribute value of each element
      if ($this->isQName($expr)) {
        foreach ($this->array as $node) {
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
  * @return object FluentDOMNodeList
  */
  public function removeAttr($name) {
    if (!empty($name)) {
      foreach ($this->array as $node) {
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
  * @return object FluentDOMNodeList
  */
  public function addClass($class) {
    return $this->toggleClass($class, TRUE);
  }
  
  /**
  * check if one element in node list has the specified css class
  *
  * @param string $class
  * @access public
  * @return object FluentDOMNodeList
  */
  public function hasClass($class) {
    foreach ($this->array as $node) {
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
  * @return object FluentDOMNodeList
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
  * @return object FluentDOMNodeList
  */
  public function toggleClass($class, $switch = NULL) {
    foreach ($this->array as $node) {
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
  * @return string | object FluentDOMNodeList
  */
  public function xml($xml = NULL) {
    if (isset($xml)) {
      if (!empty($xml)) {
        $fragment = $this->document->createDocumentFragment();
        if ($fragment->appendXML($xml)) {
          foreach ($this->array as $node) {
            $node->nodeValue = '';
            $node->appendChild($fragment->cloneNode(TRUE));
          }
        }
      }
      return $this;
    } else {
      $result = '';
      if (isset($this->array[0])) {
        foreach ($this->array[0]->childNodes as $childNode) {
          $result .= $this->document->saveXML($childNode);
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
  * @return string | object FluentDOMNodeList
  */
  public function text($text = NULL) {
    if (isset($text)) {
      foreach ($this->array as $node) {
        $node->nodeValue = $text;
      }
      return $this;
    } else {
      $result = '';
      foreach ($this->array as $node) {
        $result .= $node->textContent;
      }
      return $result;
    }
  }
  
  /**
  * append clones of elements defined by $expr to all elements in list
  *
  * @param string | object DOMNode | object FluentDOMNodeList $expr DOMNode or DOMNodeList or xml fragment string
  * @access public
  * @return string | object FluentDOMNodeList
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
  * @param string | object DOMNode | object FluentDOMNodeList $expr DOMNode or DOMNodeList or xml fragment string
  * @param boolean $first insert at first position (or last)
  * @access private
  * @return object FluentDOMNodeList
  */
  private function insertChild($expr, $first) {
    if (!empty($expr)) {
      if ($expr instanceof DOMNode) {
        foreach ($this->array as $node) {
          $node->insertBefore(
            $expr->cloneNode(TRUE),
            ($first && $node->hasChildNodes()) ? $node->childNodes->item(0) : NULL
          );
        }
      } elseif ($expr instanceof FluentDOMNodeList) {
        foreach ($this->array as $node) {
          foreach ($expr as $exprNode) {
            $node->insertBefore(
              $exprNode->cloneNode(TRUE),
              ($first && $node->hasChildNodes()) ? $node->childNodes->item(0) : NULL
            );
          }
        }
      } else {
        $fragment = $this->document->createDocumentFragment();
        if ($fragment->appendXML($expr)) {
          foreach ($this->array as $node) {
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
  * @param string | object DOMElement | object FluentDOMNodeList $expr XPath expression, element or list of elements
  * @access public
  * @return object FluentDOMNodeList list of all new elements
  */
  public function appendTo($expr) {
    return $this->insertChildTo($expr, FALSE);
  }
  
  /**
  * prepend clones of elements in the list to child nodes of all elements defined by $expr
  *
  * @param string | object DOMElement | object FluentDOMNodeList $expr XPath expression, element or list of elements
  * @access public
  * @return object FluentDOMNodeList list of all new elements
  */
  public function prependTo($expr) {
    return $this->insertChildTo($expr, TRUE);
  }
  
  /**
  * insert clones of elements in the list to child nodes of all elements defined by $expr
  *
  * @param string | object DOMElement | object FluentDOMNodeList $expr XPath expression, element or list of elements
  * @param boolean $first insert at first position (or last)
  * @access public
  * @return object FluentDOMNodeList list of all new elements
  */
  public function insertChildTo($expr, $first) {
    $result = new FluentDOMNodeList($this->document, $this);
    if (!empty($expr)) {
      if ($expr instanceof DOMElement) {
        foreach ($this->array as $node) {
          $result->add(
            $expr->insertBefore(
              $node->cloneNode(TRUE),
              ($first && $expr->hasChildNodes()) ? $expr->childNodes->item(0) : NULL
            )
          );
        }
        $node->parentNode->removeChild($node);
      } elseif ($expr instanceof FluentDOMNodeList) {
        foreach ($expr as $exprNode) {
          foreach ($this->array as $node) {
            $result->add(
              $exprNode->insertBefore(
                $node->cloneNode(TRUE),
                ($first && $exprNode->hasChildNodes()) ? $exprNode->childNodes->item(0) : NULL
              )
            );
          }
        }
        foreach ($this->array as $node) {
          $node->parentNode->removeChild($node);
        }
      } elseif (is_string($expr)) {
        $targets = $this->document->find($expr);
        foreach ($targets as $exprNode) {
          foreach ($this->array as $node) {
            $result->add(
              $exprNode->insertBefore(
                $node->cloneNode(TRUE),
                ($first && $exprNode->hasChildNodes()) ? $exprNode->childNodes->item(0) : NULL
              )
            );
          }
        }
        foreach ($this->array as $node) {
          $node->parentNode->removeChild($node);
        }
      }
    }
    return $result;
  }
}
?>