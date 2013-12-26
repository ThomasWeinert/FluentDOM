<?php

namespace FluentDOM {

  /**
   * Class Query
   *
   * @property string $contentType Output type - text/xml or text/html
   * @property-read integer $length The amount of elements found by selector.
   * @property-read \DOMDocument $document Internal DOMDocument object
   * @property-read \DOMXPath $xpath Internal XPath object
   *
   * @method Query clone() Clone matched nodes and select the clones.
   * @method bool empty() Remove all child nodes from the set of matched elements.
   */
  class Query implements \ArrayAccess, \Countable, \IteratorAggregate {

    /**
     * @var Query|NULL
     */
    private $_parent = NULL;

    /**
     * @var array(\DOMNode)
     */
    private $_nodes = array();

    /**
     * @var Xpath
     */
    private $_xpath = NULL;

    /**
     * @var \DOMDocument
     */
    private $_document = NULL;

    /**
     * Content type for output (xml, text/xml, html, text/html).
     * @var string $_contentType
     */
    private $_contentType = 'text/xml';

    /**
     * Use document context for expression (not selected nodes).
     * @var boolean $_useDocumentContext
     */
    private $_useDocumentContext = TRUE;

    /**
     * Load a $source. The type of the source depends on the loaders. If no explicit loaders are set
     * FluentDOM\Query will use a set of default loaders for xml/html and DOM.
     *
     * @param mixed $source
     * @param string $contentType optional, default value 'text/xml'
     */
    public function load($source, $contentType = 'text/xml') {
      $dom = FALSE;
      $this->_useDocumentContext = TRUE;
      if ($source instanceof Query) {
        $dom = $source->getDocument();
      } elseif ($source instanceof \DOMDocument) {
        $dom = $source;
      } elseif ($source instanceof \DOMNode) {
        $dom = $source->ownerDocument;
        $this->_nodes = array($source);
        $this->_useDocumentContext = FALSE;
      }
      if ($dom instanceof \DOMDocument) {
        $this->_document = $dom;
        $this->setContentType($contentType);
        return $this;
      } else {
        throw new \InvalidArgumentException(
          "Can not load: ".(is_object($source) ? get_class($source) : gettype($source))
        );
      }
    }

    /*
     * Core functions
     */

    /**
     * Formats the current document, resets internal node array and other properties.
     *
     * The document is saved and reloaded, all variables with DOMNodes
     * of this document will get invalid.
     *
     * @param string $contentType
     * @return Query
     */
    public function formatOutput($contentType = NULL) {
      if (isset($contentType)) {
        $this->setContentType($contentType);
      }
      $this->_array = array();
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
     * The item() method is used to access elements in the node list,
     * like in a DOMNodelist.
     *
     * @param integer $position
     * @return \DOMNode
     */
    public function item($position) {
      if (isset($this->_nodes[$position])) {
        return $this->_nodes[$position];
      }
      return NULL;
    }

    /**
     * @return Xpath
     */
    public function xpath() {
      if ($this->_document instanceof Document) {
        return $this->_document->xpath();
      } elseif (isset($this->_xpath) && $this->_xpath->document === $this->_document) {
        return $this->_xpath;
      } else {
        $this->_xpath = new Xpath($this->_document);
        return $this->_xpath;
      }
    }

    /**
     * Create a new instance of the same class with $this as the parent. This is used for the chaining.
     *
     * @param \Traversable|\DOMNode|Query $elements
     * @return Query
     */
    public function spawn($elements = NULL) {
      /**
       * @var Query $result
       */
      $result = new $this;
      $result->_parent = $this;
      $result->_document = $this->_document;
      $result->_xpath = $this->_xpath;
      $result->_contentType = $this->contentType;
      if (isset($elements)) {
        $result->push($elements);
      }
      return $result;
    }

    /**
     * Push new element(s) an the internal element list
     *
     * @uses _inList
     * @param \DOMNode|\DOMNodeList|Query $elements
     * @param boolean $ignoreTextNodes ignore text nodes
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function push($elements, $ignoreTextNodes = FALSE) {
      if ($this->isNode($elements, $ignoreTextNodes)) {
        $elements = array($elements);
      }
      if ($this->isNodeList($elements)) {
        $this->_useDocumentContext = FALSE;
        foreach ($elements as $index => $node) {
          if ($this->isNode($node, $ignoreTextNodes)) {
            if ($node->ownerDocument === $this->_document) {
              $this->_nodes[] = $node;
            } else {
              throw new \OutOfBoundsException(
                sprintf(
                  'Node #%d is not a part of this document', $index
                )
              );
            }
          }
        }
      } elseif (!is_null($elements)) {
        throw new \InvalidArgumentException('Invalid elements variable.');
      }
    }

    /**
     * Sorts an array of DOM nodes based on document position, in place, with the duplicates removed.
     * Note that this only works on arrays of DOM nodes, not strings or numbers.
     *
     * @param array $array array of DOM nodes
     * @throws \InvalidArgumentException
     * @return array
     */
    public function unique(array $array) {
      $sortable = array();
      $unsortable = array();
      foreach ($array as $node) {
        if (!($node instanceof \DOMNode)) {
          throw new \InvalidArgumentException(
            sprintf(
              'Array must only contain dom nodes, found "%s".',
              is_object($node) ? get_class($node) : gettype($node)
            )
          );
        }
        if (isset($node->parentNode) ||
          $node === $node->ownerDocument->documentElement) {
          $position = (integer)$this->xpath()->evaluate('count(preceding::node())', $node);
          /* use the document position as index, ignore duplicates */
          if (!isset($sortable[$position])) {
            $sortable[$position] = $node;
          }
        } else {
          $hash = spl_object_hash($node);
          /* use the object hash as index, ignore duplicates */
          if (!isset($unsortable[$hash])) {
            $unsortable[$hash] = $node;
          }
        }
      }
      ksort($sortable, SORT_NUMERIC);
      $result = array_values($sortable);
      array_splice($result, count($result), 0, array_values($unsortable));
      return $result;
    }

    /*
     * Interfaces
     */

    /**
     * Countable interface
     *
     * @return int
     */
    public function count() {
      return count($this->_nodes);
    }

    /**
     * IteratorAggregate interface
     *
     * @return Query\Iterator|\Traversable
     */
    public function getIterator() {
      return new Query\Iterator($this);
    }/*
  * Interface - ArrayAccess
  */

    /**
     * Check if index exists in internal array
     *
     * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
     * @param integer $offset
     * @return boolean
     */
    public function offsetExists($offset) {
      return isset($this->_nodes[$offset]);
    }

    /**
     * Get element from internal array
     *
     * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
     * @param integer $offset
     * @return \DOMNode|NULL
     */
    public function offsetGet($offset) {
      return isset($this->_nodes[$offset]) ? $this->_nodes[$offset] : NULL;
    }

    /**
     * If somebody tries to modify the internal array throw an exception.
     *
     * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
     * @param integer $offset
     * @param mixed $value
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value) {
      throw new \BadMethodCallException('List is read only');
    }

    /**
     * If somebody tries to remove an element from the internal array throw an exception.
     *
     * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
     * @param integer $offset
     * @throws \BadMethodCallException
     */
    public function offsetUnset($offset) {
      throw new \BadMethodCallException('List is read only');
    }

    /**
     * Virtual properties, validate existence
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
      switch ($name) {
      case 'length' :
      case 'xpath' :
      case 'contentType' :
        return TRUE;
      case 'document' :
        return isset($this->_document);
      }
      return FALSE;
    }

    /**
     * Virtual properties, read property
     *
     * @param string $name
     * @return mixed
     * @throws \LogicException
     */
    public function __get($name) {
      switch ($name) {
      case 'contentType' :
        return $this->_contentType;
      case 'document' :
        return $this->getDocument();
      case 'length' :
        return count($this->_nodes);
      case 'xpath' :
        return $this->xpath();
      default :
        return NULL;
      }
    }

    /**
     * Block changing the readonly dynamic property
     *
     * @param string $name
     * @param mixed $value
     * @throws \BadMethodCallException
     */
    public function __set($name, $value) {
      switch ($name) {
      case 'contentType' :
        $this->setContentType($value);
        break;
      case 'document' :
      case 'length' :
      case 'xpath' :
        throw new \BadMethodCallException('Can not set readonly value.');
      default :
        $this->$name = $value;
        break;
      }
    }

    /**
     * Throws an exception if somebody tries to unset one
     * of the dznamic properties
     *
     * @param string $name
     * @throws \BadMethodCallException
     */
    public function __unset($name) {
      switch ($name) {
      case 'contentType' :
      case 'document' :
      case 'length' :
      case 'xpath' :
        throw new \BadMethodCallException('Can not remove property.');
      default :
        unset($this->$name);
        break;
      }
    }

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
        return $this->emptyNodes();
      case 'clone' :
        return $this->cloneNodes();
      default :
        throw new \BadMethodCallException('Unknown method '.get_class($this).'::'.$name);
      }
    }

    /**
     * Return the XML output of the internal dom document
     *
     * @return string
     */
    public function __toString() {
      switch ($this->_contentType) {
      case 'html' :
      case 'text/html' :
        return $this->getDocument()->saveHTML();
      default :
        return $this->getDocument()->saveXML();
      }
    }

    /*
     * Traversing
     */

    /**
     * @return Query|NULL
     */
    public function end() {
      return $this->_parent;
    }

    /**
     * Check if the DOMNode is DOMElement or DOMText with content
     *
     * @param \DOMNode $node
     * @param boolean $ignoreTextNodes
     * @return boolean
     */
    private function isNode($node, $ignoreTextNodes = FALSE) {
      if (is_object($node)) {
        if ($node instanceof \DOMElement) {
          return TRUE;
        } elseif ($node instanceof \DOMText) {
          if (!$ignoreTextNodes &&
            !$node->isWhitespaceInElementContent()) {
            return TRUE;
          }
        }
      }
      return FALSE;
    }

    /**
     * Check if $elements is a traversable node list
     *
     * @param array|\Traversable $elements
     * @return boolean
     */
    private function isNodeList($elements) {
      if ($elements instanceof \Traversable ||
          is_array($elements)) {
        return TRUE;
      }
      return FALSE;
    }

    /**
     * Setter for Query::_contentType property
     *
     * @param string $value
     * @throws \UnexpectedValueException
     */
    private function setContentType($value) {
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
        throw new \UnexpectedValueException('Invalid content type value');
      }
      if ($this->_contentType != $newContentType) {
        $this->_contentType = $newContentType;
        if (isset($this->_parent)) {
          $this->_parent->contentType = $newContentType;
        }
      }
    }

    private function getDocument() {
      if (!($this->_document instanceof \DOMDocument)) {
        $this->_document = new Document();
      }
      return $this->_document;
    }

    /**
     * Match XPath expression against context and return matched elements.
     *
     * @param string $expr
     * @param \DOMNode $context optional, default value NULL
     * @throws \InvalidArgumentException
     * @return \DOMNodeList
     */
    private function getNodes($expr, \DOMNode $context = NULL) {
      $list = $this->xpath()->evaluate($expr, $context, FALSE);
      if ($list instanceof \DOMNodeList) {
        return $list;
      } else {
        throw new \InvalidArgumentException('Given xpath expression did not return an node list.');
      }
    }

    /*********************
     * Traversing
     ********************/

    /**
     * Execute a function within the context of every matched element.
     *
     * @param callable $function
     * @return Query
     */
    public function each(callable $function) {
      foreach ($this->_nodes as $index => $node) {
        call_user_func($function, $node, $index);
      }
      return $this;
    }

    /**
     * Searches for descendant elements that match the specified expression.
     *
     * @example find.php Usage Example: FluentDOM::find()
     * @param string $expr XPath expression
     * @param boolean $useDocumentContext ignore current node list
     * @return Query
     */
    public function find($expr, $useDocumentContext = FALSE) {
      $result = $this->spawn();
      if ($useDocumentContext ||
        $this->_useDocumentContext) {
        $result->push($this->getNodes($expr));
      } else {
        foreach ($this->_nodes as $contextNode) {
          $result->push($this->getNodes($expr, $contextNode));
        }
      }
      return $result;
    }

    /*********************
     * Manipulation
     ********************/

    /**
     * Clone matched DOM Elements and select the clones.
     *
     * This is the clone() method - but because clone
     * is a reserved word we can no declare it directly
     * @see __call
     *
     * @example clone.php Usage Example: FluentDOM\Query:clone()
     * @return Query
     */
    private function cloneNodes() {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        /**
         * @var \DOMNode $node
         */
        $result->push($node->cloneNode(TRUE));
      }
      return $result;
    }

    /**
     * Remove all child nodes from the set of matched elements.
     *
     * This is the empty() method - but because empty
     * is a reserved word we can no declare it directly
     * @see __call
     *
     * @example empty.php Usage Example: FluentDOM\Query:empty()
     * @return Query
     */
    private function emptyNodes() {
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement ||
            $node instanceof \DOMText ||
            $node instanceof \DOMCdataSection) {
          $node->nodeValue = '';
        }
      }
      $this->_useDocumentContext = TRUE;
      return $this;
    }
  }
}