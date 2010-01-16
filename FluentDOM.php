<?php
/**
* FluentDOM implements a jQuery like replacement for DOMNodeList
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @tutorial FluentDOM.pkg
* @package FluentDOM
*/

/**
* Include the external iterator class.
*/
require_once(dirname(__FILE__).'/FluentDOM/Iterator.php');
/**
* Include the loader interface.
*/
require_once(dirname(__FILE__).'/FluentDOM/Loader.php');

/**
* Function to create a new FluentDOM instance and loads data into it if
* a valid $source is provided.
*
* @param mixed $source
* @param string $contentType optional, default value 'text/xml'
* @access public
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
* @property string $contentType Output type - text/xml or text/html
* @property-read integer $length The amount of elements found by selector.
* @property-read DOMDocument $document Internal DOMDocument object
* @property-read DOMXPath $xpath Internal XPath object
*
* @method bool empty() Remove all child nodes from the set of matched elements.
* @method DOMDocument clone() Clone matched DOM Elements and select the clones.
*
* @package FluentDOM
*/
class FluentDOM implements IteratorAggregate, Countable, ArrayAccess {

  /**
  * Associated DOMDocument object.
  * @var DOMDocument
  * @access private
  */
  private $_document = NULL;

  /**
  * XPath object used to execute selectors
  * @var DOMXPath
  * @access private
  */
  private $_xpath = NULL;

  /**
  * Use document context for expression (not selected nodes).
  * @var boolean
  * @access private
  */
  private $_useDocumentContext = TRUE;

  /**
  * Content type for output (xml, text/xml, html, text/html).
  * @var string
  * @access private
  */
  private $_contentType = 'text/xml';

  /**
  * Parent FluentDOM object (previous selection in chain).
  * @var FluentDOM
  * @access private
  */
  private $_parent = NULL;

  /**
  * Seleted element and text nodes
  * @var array
  * @access protected
  */
  protected $_array = array();

  /**
  * Document loader list.
  *
  * @see _initLoaders
  * @see _setLoader
  *
  * @var array
  * @access private
  */
  private $_loaders = NULL;

  /**
  * Constructor
  *
  * @access public
  * @return FluentDOM
  */
  public function __construct() {
    $this->_document = new DOMDocument();
  }

  /**
  * Load a $source. The type of the source depends on the loaders. If no explicit loaders are set
  * FluentDOM will use a set of default loaders for xml/html and DOM.
  *
  * @param mixed $source
  * @param string $contentType optional, default value 'text/xml'
  * @access public
  */
  public function load($source, $contentType = 'text/xml') {
    $this->_array = array();
    $this->_setContentType($contentType);
    if ($source instanceof FluentDOM) {
      $this->_useDocumentContext = FALSE;
      $this->_document = $source->document;
      $this->_xpath = $source->_xpath;
      $this->_contentType = $source->_contentType;
      $this->_parent = $source;
      return $this;
    } else {
      $this->_parent = NULL;
      $this->_initLoaders();
      foreach ($this->_loaders as $loader) {
        if ($loaded = $loader->load($source, $this->_contentType)) {
          if ($loaded instanceof DOMDocument) {
            $this->_useDocumentContext = TRUE;
            $this->_document = $loaded;
          } elseif (is_array($loaded) &&
                    isset($loaded[0]) &&
                    isset($loaded[1]) &&
                    $loaded[0] instanceof DOMDocument &&
                    is_array($loaded[1])) {
            $this->_document = $loaded[0];
            $this->_push($loaded[1]);
            $this->_useDocumentContext = FALSE;
          }
          return $this;
        }
      }
      throw new InvalidArgumentException('Invalid source object.');
    }
    return $this;
  }

  /**
  * Initialize default loaders if they are not already initialized
  *
  * @access protected
  * @return void
  */
  protected function _initLoaders() {
    if (!is_array($this->_loaders)) {
      $path = dirname(__FILE__).'/FluentDOM';
      include_once($path.'/Loader/DOMNode.php');
      include_once($path.'/Loader/DOMDocument.php');
      include_once($path.'/Loader/StringXML.php');
      include_once($path.'/Loader/FileXML.php');
      include_once($path.'/Loader/StringHTML.php');
      include_once($path.'/Loader/FileHTML.php');
      $this->_loaders = array(
        new FluentDOMLoaderDOMNode(),
        new FluentDOMLoaderDOMDocument(),
        new FluentDOMLoaderStringXML(),
        new FluentDOMLoaderFileXML(),
        new FluentDOMLoaderStringHTML(),
        new FluentDOMLoaderFileHTML(),
      );
    }
  }

  /**
  * Define own loading handlers
  *
  * @example iniloader/iniToXML.php Usage Example: Own loader object
  * @param $loaders
  * @access public
  * @return FluentDOM
  */
  public function setLoaders($loaders) {
    foreach ($loaders as $loader) {
      if (!($loader instanceof FluentDOMLoader)) {
        throw new InvalidArgumentException('Array contains invalid loader object');
      }
    }
    $this->_loaders = $loaders;
    return $this;
  }

  /**
  * Setter for FluentDOM::_contentType property
  *
  * @param string $value
  * @access private
  * @return void
  */
  private function _setContentType($value) {
    switch (strtolower($value)) {
    case 'xml' :
    case 'application/xml' :
    case 'text/xml' :
      $newContentType = 'text/xml';
      break;
    case 'html' :
    case 'text/html' :
      $newContentType = 'text/html';
      break;
    default :
      throw new UnexpectedValueException('Invalid content type value');
    }
    if ($this->_contentType != $newContentType) {
      $this->_contentType = $newContentType;
      if (isset($this->_parent)) {
        $this->_parent->contentType = $newContentType;
      }
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
    case 'contentType' :
      return $this->_contentType;
    case 'document' :
      return $this->_document;
    case 'length' :
      return count($this->_array);
    case 'xpath' :
      return $this->_xpath();
    default :
      return NULL;
    }
  }

  /**
  * block changes of dynamic readonly property length
  *
  * @param string $name
  * @param mixed $value
  * @access public
  * @return void
  */
  public function __set($name, $value) {
    switch ($name) {
    case 'contentType' :
      $this->_setContentType($value);
      break;
    case 'document' :
    case 'length' :
    case 'xpath' :
      throw new BadMethodCallException('Can not set readonly value.');
    default :
      $this->$name = $value;
      break;
    }
  }

  /**
  * support isset for dynamic properties length and document
  *
  * @param string $name
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
  * declaring an empty() or clone() method will crash the parser so we use some magic
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
    default :
      throw new BadMethodCallException('Unknown method '.get_class($this).'::'.$name);
    }
  }

  /**
  * Return the XML output of the internal dom document
  *
  * @access public
  * @return string
  */
  public function __toString() {
    switch ($this->_contentType) {
    case 'html' :
    case 'text/html' :
      return $this->_document->saveHTML();
    default :
      return $this->_document->saveXML();
    }
  }

  /**
  * The item() method is used to access elements in the node list,
  * like in a DOMNodelist.
  *
  * @param integer $position
  * @access public
  * @return DOMNode
  */
  public function item($position) {
    if (isset($this->_array[$position])) {
      return $this->_array[$position];
    }
    return NULL;
  }

  /*
  * Interface - IteratorAggregate
  */

  /**
  * Get an iterator for this object.
  *
  * @example interfaces/Iterator.php Usage Example: Iterator Interface
  * @example interfaces/RecursiveIterator.php Usage Example: Recursive Iterator Interface
  * @return FluentDOMIterator
  */
  public function getIterator() {
    return new FluentDOMIterator($this);
  }

  /*
  * Interface - Countable
  */

  /**
  * Get element count (Countable interface)
  *
  * @example interfaces/Countable.php Usage Example: Countable Interface
  * @access public
  * @return integer
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
  * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
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
  * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
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
  * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
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
  * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
  * @param $offset
  * @access public
  * @return void
  */
  public function offsetGet($offset) {
    return isset($this->_array[$offset]) ? $this->_array[$offset] : NULL;
  }

  /*
  * Core functions
  */

  /**
  * Create a new instance of the same class with $this as the parent. This is used for the chaining.
  *
  * @access private
  * @return  FluentDOM
  */
  private function _spawn() {
    $className = get_class($this);
    $result = new $className();
    return $result->load($this);
  }

  /**
  * Get a XPath object associated with the internal DOMDocument and register
  * default namespaces from the document element if availiable.
  *
  * @access private
  * @return DOMXPath
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
  * Match XPath expression agains context and return matched elements.
  *
  * @param string $expr
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
  * Test xpath expression against context and return true/false
  *
  * @param string $expr
  * @param DOMElement $context optional, default value NULL
  * @access private
  * @return boolean
  */
  private function _test($expr, $context) {
    $check = $this->_xpath()->evaluate($expr, $context);
    if ($check instanceof DOMNodeList) {
      return $check->length > 0;
    } else {
      return (bool)$check;
    }
  }

  /**
  * Push new element(s) an the internal element list
  *
  * @uses _inList
  * @param DOMElement|DOMNodeList|FluentDOM $elements
  * @param boolean $unique ignore duplicates
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
              $elements instanceof IteratorAggregate ||
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
  * Check if object is already in internal list
  *
  * @param DOMElement $node
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
  * Validate string as qualified node name
  *
  * @param string $name
  * @access private
  * @return boolean
  */
  private function _isQName($name) {
    if (empty($name)) {
      throw new UnexpectedValueException('Invalid QName: QName is empty.');
    } elseif (FALSE !== strpos($name, ':')) {
      list($namespace, $localName) = explode(':', $name, 2);
      $this->_isNCName($namespace);
      $this->_isNCName($localName, strlen($namespace));
      return TRUE;
    }
    $this->_isNCName($name);
    return TRUE;
  }

  /**
  * Validate string as qualified node name part (namespace or local name)
  *
  * @param string $name
  * @param integer $offset index offset for excpetion messages
  * @access private
  * @return boolean
  */
  private function _isNCName($name, $offset = 0) {
    $nameStartChar =
       'A-Z_a-z'.
       '\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}'.
       '\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}'.
       '\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}'.
       '\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}';
    $nameChar =
       $nameStartChar.
       '\\.\\d\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}';
    if (empty($name)) {
      throw new UnexpectedValueException(
        'Invalid QName "'.$name.'": Missing QName part.'
      );
    } elseif (preg_match('([^'.$nameChar.'])u', $name, $match, PREG_OFFSET_CAPTURE)) {
      //invalid bytes and whitespaces
      $position = (int)$match[0][1];
      throw new UnexpectedValueException(
        'Invalid QName "'.$name.'": Invalid character at index '.($offset + $position).'.'
      );
    } elseif (preg_match('(^[^'.$nameStartChar.'-])u', $name)) {
      //first char is a little more limited
      throw new UnexpectedValueException(
        'Invalid QName "'.$name.'": Invalid character at .index '.$offset.'.'
      );
    }
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
  * check if parameter is a valid callback function
  *
  * @param callback $callback
  * @param boolean $allowGlobalFunctions
  * @access protected
  * @return boolean
  */
  protected function _isCallback($callback, $allowGlobalFunctions = TRUE) {
    if ($callback instanceof Closure) {
      return TRUE;
    } elseif ($allowGlobalFunctions &&
              is_string($callback) &&
              function_exists($callback)) {
      return is_callable($callback);
    } elseif (is_array($callback) &&
              count($callback) == 2 &&
              (is_object($callback[0]) || is_string($callback[0])) &&
              is_string($callback[1])) {
      return is_callable($callback);
    } else {
      throw new InvalidArgumentException('Invalid callback argument');
    }
  }

  /**
  * Convert a given content xml string into and array of nodes
  *
  * @param string $content
  * @param boolean $includeTextNodes
  * @param integer $limit
  * @access private
  * @return array
  */
  private function _getContentFragment($content, $includeTextNodes = TRUE, $limit = 0) {
    $result = array();
    $fragment = $this->_document->createDocumentFragment();
    if ($fragment->appendXML($content)) {
      for ($i = $fragment->childNodes->length - 1; $i >= 0; $i--) {
        $element = $fragment->childNodes->item($i);
        if ($element instanceof DOMElement ||
            ($includeTextNodes && $this->_isNode($element))) {
          array_unshift($result, $element);
          $element->parentNode->removeChild($element);
        }
      }
      if ($limit > 0 && count($result) >= $limit) {
        return array_slice($result, 0, $limit);
      }
      return $result;
    } else {
      throw new UnexpectedValueException('Invalid document fragment');
    }
  }

  /**
  * Convert a given content into and array of nodes
  *
  * @param string|array|DOMNode|Iterator $content
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
      $result = $this->_getContentFragment($content, $includeTextNodes, $limit);
    } elseif ($content instanceof DOMNodeList ||
              $content instanceof Iterator ||
              $content instanceof IteratorAggregate ||
              is_array($content)) {
      foreach ($content as $element) {
        if ($element instanceof DOMElement ||
            ($includeTextNodes && $this->_isNode($element))) {
          $result[] = $element;
          if ($limit > 0 && count($result) >= $limit) {
            break;
          }
        }
      }
    } else {
      throw new InvalidArgumentException('Invalid content parameter');
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

  /**
  * Get the target nodes from a given $selector.
  *
  * A string will be used as XPath expression.
  *
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @return unknown_type
  */
  private function _getTargetNodes($selector) {
    if ($this->_isNode($selector)) {
      return array($selector);
    } elseif (is_string($selector)) {
      return $this->_match($selector);
    } elseif (is_array($selector) ||
              $selector instanceof Iterator ||
              $selector instanceof IteratorAggregate ||
              $selector instanceof DOMNodeList) {
      return $selector;
    } else {
      throw new InvalidArgumentException('Invalid selector');
    }
  }

  /**
  * Remove nodes from document tree
  *
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
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
  * Convert $content to a DOMElement. If $content contains several elements use the first.
  *
  * @param string|array|DOMNode|Iterator $content
  * @access private
  * @return DOMElement
  */
  private function _getContentElement($content) {
    if ($content instanceof DOMElement) {
      return $content;
    } else {
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
  * @param callback $function
  * @access public
  * @return FluentDOM
  */
  public function each($function) {
    if ($this->_isCallback($function)) {
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
  * Formats the current document, resets internal node array and other properties.
  *
  * The document is saved and reloaded, all variables with DOMNodes
  * of this document will get invalid.
  *
  * @access public
  * @return FluentDOM
  */
  public function formatOutput($contentType = NULL) {
    if (isset($contentType)) {
      $this->_setContentType($contentType);
    }
    $this->_array = array();
    $this->_position = 0;
    $this->_useDocumentContext = TRUE;
    $this->_parent = NULL;
    $this->_document->preserveWhiteSpace = FALSE;
    $this->_document->formatOutput = TRUE;
    if (!empty($this->_document->documentElement)) {
      $this->_document->loadXML($this->_document->saveXML());
    }
    return $this;
  }

  /**
  * Retrieve the matched DOM elements in an array.
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
  * @access public
  * @return FluentDOM
  */
  public function eq($position) {
    $result = $this->_spawn();
    if ($position < 0) {
      $position = count($this->_array) + $position;
    }
    if (isset($this->_array[$position])) {
      $result->_push($this->_array[$position]);
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
  * @access public
  * @return FluentDOM
  */
  public function filter($expr) {
    $result = $this->_spawn();
    foreach ($this->_array as $index => $node) {
      $check = TRUE;
      if (is_string($expr)) {
        $check = $this->_test($expr, $node, $index);
      } elseif ($this->_isCallback($expr)) {
        $check = call_user_func($expr, $node, $index);
      }
      if ($check) {
        $result->_push($node);
      }
    }
    return $result;
  }

  /**
  * Retrieve the matched DOM elements in an array. A negative position will be counted from the end.
  * @parameter integer|NULL optional offset of a single element to get.
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
  * Checks the current selection against an expression and returns true,
  * if at least one element of the selection fits the given expression.
  *
  * @example is.php Usage Example: FluentDOM::is()
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
  * If the callback function returns an array each element of the array will be added to the
  * result array. All other variable types are put directly into the result array.
  *
  * @example map.php Usage Example: FluentDOM::map()
  * @param callback $function
  * @access public
  * @return array
  */
  public function map($function) {
    $result = array();
    foreach ($this->_array as $index => $node) {
      if ($this->_isCallback($function)) {
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
  * @access public
  * @return FluentDOM
  */
  public function not($expr) {
    $result = $this->_spawn();
    foreach ($this->_array as $index => $node) {
      $check = FALSE;
      if (is_string($expr)) {
        $check = $this->_test($expr, $node, $index);
      } elseif ($this->_isCallback($expr)) {
        $check = call_user_func($expr, $node, $index);
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
  * @example slice.php Usage Example: FluentDOM::slice()
  * @param integer $start
  * @param integer $end
  * @access public
  * @return FluentDOM
  */
  public function slice($start, $end = NULL) {
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
  * @example add.php Usage Examples: FluentDOM::add()
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
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
  * @example children.php Usage Examples: FluentDOM::children()
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  public function children($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      if (empty($expr)) {
        $result->_push($node->childNodes, TRUE);
      } else {
        foreach ($node->childNodes as $childNode) {
          if ($this->_test($expr, $childNode)) {
            $result->_push($childNode, TRUE);
          }
        }
      }
    }
    return $result;
  }

  /**
  * Searches for descendent elements that match the specified expression.
  *
  * @example find.php Usage Example: FluentDOM::find()
  * @param string $expr XPath expression
  * @param boolean $useDocumentContext ignore current node list
  * @access public
  * @return FluentDOM
  */
  public function find($expr, $useDocumentContext = FALSE) {
    $result = $this->_spawn();
    if ($useDocumentContext ||
        $this->_useDocumentContext) {
      $result->_push($this->_match($expr));
    } else {
      foreach ($this->_array as $contextNode) {
        $result->_push($this->_match($expr, $contextNode));
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
  * @access public
  * @return FluentDOM
  */
  public function next($expr = NULL) {
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
  * @example nextAll.php Usage Example: FluentDOM::nextAll()
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  public function nextAll($expr = NULL) {
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
  * @example parent.php Usage Example: FluentDOM::parent()
  * @access public
  * @return FluentDOM
  */
  public function parent() {
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
  * @example parents.php Usage Example: FluentDOM::parents()
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  public function parents($expr = NULL) {
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
  * Get a set of elements containing the unique previous siblings of each of the
  * matched set of elements.
  *
  * @example prev.php Usage Example: FluentDOM::prev()
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  public function prev($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      $previous = $node->previousSibling;
      while ($previous instanceof DOMNode && !$this->_isNode($previous)) {
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
  * @example prevAll.php Usage Example: FluentDOM::prevAll()
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  public function prevAll($expr = NULL) {
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
  * Get a set of elements containing all of the unique siblings of each of the
  * matched set of elements.
  *
  * @example siblings.php Usage Example: FluentDOM::siblings()
  * @param string $expr XPath expression
  * @access public
  * @return FluentDOM
  */
  public function siblings($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
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

  /**
  * Get a set of elements containing the closest parent element that matches the specified
  * selector, the starting element included.
  *
  * @example closest.php Usage Example: FluentDOM::closest()
  * @param string $expr XPath expression
  * @return FluentDOM
  */
  public function closest($expr) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      while (isset($node)) {
        if ($this->_test($expr, $node)) {
          $result->_push($node, TRUE);
          break;
        }
        $node = $node->parentNode;
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
  * @return FluentDOM
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
  * @return FluentDOM
  */
  public function end() {
    if ($this->_parent instanceof FluentDOM) {
      return $this->_parent;
    } else {
      return $this;
    }
  }

  /*
  * Manipulation - Changing Contents
  */

  private function _getInnerXml($node) {
    $result = '';
    foreach ($node->childNodes as $childNode) {
      if ($this->_isNode($childNode)) {
        $result .= $this->_document->saveXML($childNode);
      }
    }
    return $result;
  }

  /**
  * Get or set the xml contents of the first matched element.
  *
  * @example xml.php Usage Example: FluentDOM::xml()
  * @param string|callback|object Closure $xml XML fragment
  * @access public
  * @return string|FluentDOM
  */
  public function xml($xml = NULL) {
    if (isset($xml)) {
      try {
        $isCallback = $this->_isCallback($xml, FALSE);
      } catch (InvalidArgumentException $e) {
        $isCallback = FALSE;
      }
      if ($isCallback) {
        foreach ($this->_array as $index => $node) {
          $xmlString = call_user_func(
            $xml,
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
  * @param string $text
  * @access public
  * @return string|FluentDOM
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
  * @example append.php Usage Example: FluentDOM::append()
  * @param string|array|DOMNode|Iterator $content DOMNode or DOMNodeList or xml fragment string
  * @access public
  * @return FluentDOM
  */
  public function append($content) {
    return $this->_insertChild($content, FALSE);
  }

  /**
  * Append all of the matched elements to another, specified, set of elements.
  * Returns all of the inserted elements.
  *
  * @example appendTo.php Usage Example: FluentDOM::appendTo()
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @access public
  * @return FluentDOM
  */
  public function appendTo($selector) {
    return $this->_insertChildTo($selector, FALSE);
  }

  /**
  * Prepend content to the inside of every matched element.
  *
  * @example prepend.php Usage Example: FluentDOM::prepend()
  * @param string|array|DOMNode|Iterator $content
  * @access public
  * @return FluentDOM
  */
  public function prepend($content) {
    return $this->_insertChild($content, TRUE);
  }

  /**
  * Prepend all of the matched elements to another, specified, set of elements.
  * Returns all of the inserted elements.
  *
  * @example prependTo.php Usage Example: FluentDOM::prependTo()
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @access public
  * @return FluentDOM list of all new elements
  */
  public function prependTo($selector) {
    return $this->_insertChildTo($selector, TRUE);
  }

  /**
  * Insert content to the inside of every matched element.
  *
  * @param string|array|DOMNode|Iterator $content
  * @param boolean $first insert at first position (or last)
  * @access private
  * @return FluentDOM
  */
  private function _insertChild($content, $first) {
    $result = $this->_spawn();
    if (empty($this->_array) &&
        $this->_useDocumentContext &&
        !isset($this->_document->documentElement)) {
      $contentNode = $this->_getContentElement($content);
      $result->_push(
        $this->_document->appendChild(
          $contentNode
        )
      );
    } else {
      $contentNodes = $this->_getContentNodes($content, TRUE);
      foreach ($this->_array as $node) {
        foreach ($contentNodes as $contentNode) {
          $result->_push(
            $node->insertBefore(
              $contentNode->cloneNode(TRUE),
              ($first && $node->hasChildNodes()) ? $node->childNodes->item(0) : NULL
            )
          );
        }
      }
    }
    return $result;
  }

  /**
  * Insert all of the matched elements to another, specified, set of elements.
  * Returns all of the inserted elements.
  *
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @param boolean $first insert at first position (or last)
  * @access public
  * @return FluentDOM
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
  * @example after.php Usage Example: FluentDOM::after()
  * @param string|array|DOMNode|Iterator $content
  * @access public
  * @return FluentDOM
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
  * @example before.php Usage Example: FluentDOM::before()
  * @param string|array|DOMNode|Iterator $content
  * @access public
  * @return FluentDOM
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
  * @example insertAfter.php Usage Example: FluentDOM::insertAfter()
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @access public
  * @return FluentDOM
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
  * @example insertBefore.php Usage Example: FluentDOM::insertBefore()
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @access public
  * @return FluentDOM
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
  * @param string|array|DOMNode|Iterator $content
  * @access private
  * @return FluentDOM
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
  * @example wrap.php Usage Example: FluentDOM::wrap()
  * @param string|array|DOMNode|Iterator $content
  * @access public
  * @return FluentDOM
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
  * @example wrapAll.php Usage Example: FluentDOM::wrapAll()
  * @param string|array|DOMNode|Iterator $content
  * @access public
  * @return FluentDOM
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
  * @example wrapInner.php Usage Example: FluentDOM::wrapInner()
  * @param string|array|DOMNode|Iterator $content
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
  * @example replaceWith.php Usage Example: FluentDOM::replaceWith()
  * @param string|array|DOMNode|Iterator $content
  * @access public
  * @return FluentDOM
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
  * @example replaceAll.php Usage Example: FluentDOM::replaceAll()
  * @param string|array|DOMNode|DOMNodeList|FluentDOM $selector
  * @access public
  * @return FluentDOM
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
  * Remove all child nodes from the set of matched elements.
  *
  * This is the empty() method - but because empty
  * is a reserved word we can no declare it directly
  * @see __call
  *
  * @example empty.php Usage Example: FluentDOM:empty()
  * @access private
  * @return FluentDOM
  */
  private function _emptyNodes() {
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
  * @access public
  * @return FluentDOM removed elements
  */
  public function remove($expr = NULL) {
    $result = $this->_spawn();
    foreach ($this->_array as $node) {
      if (isset($node->parentNode)) {
        if (empty($expr) || $this->_test($expr, $node)) {
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
  * Create nodes list from content, if $content contains node(s)
  * from another document the are imported.
  *
  * @example node.php Usage Example: FluentDOM::node()
  * @param string|array|DOMNode|Iterator $content
  * @access public
  * @return FluentDOM
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
  * This is the clone() method - but because clone
  * is a reserved word we can no declare it directly
  * @see __call
  *
  * @example clone.php Usage Example: FluentDOM:clone()
  * @access private
  * @return FluentDOM
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
  * @example attr.php Usage Example: FluentDOM:attr() Read an attribute value.
  * @param string|array $attribute attribute name or attribute list
  * @param string|callback $value function callback($index, $value) or value
  * @access public
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
            $node->setAttribute(
              $attribute,
              call_user_func($value, $index, $node->getAttribute($attribute))
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
  * @access public
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

  /*
  * Attributes - Classes
  */

  /**
  * Adds the specified class(es) to each of the set of matched elements.
  *
  * @param string $class
  * @access public
  * @return FluentDOM
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
  * @param string|array $class
  * @access public
  * @return FluentDOM
  */
  public function removeClass($class) {
    return $this->toggleClass($class, FALSE);
  }

  /**
  * Adds the specified class if the switch is TRUE,
  * removes the specified class if the switch is FALSE,
  * toggles the specified class if the switch is NULL.
  *
  * @example toggleClass.php Usage Example: FluentDOM::toggleClass()
  * @param string $class
  * @param NULL|boolean $switch toggle if NULL, add if TRUE, remove if FALSE
  * @access public
  * @return FluentDOM
  */
  public function toggleClass($class, $switch = NULL) {
    foreach ($this->_array as $node) {
      if ($node instanceof DOMElement) {
        if ($node->hasAttribute('class')) {
          $currentClasses = array_flip(
            preg_split('(\s+)', trim($node->getAttribute('class')))
          );
        } else {
          $currentClasses = array();
        }
        $toggledClasses = array_unique(preg_split('(\s+)', trim($class)));
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
    return $this;
  }
}
?>