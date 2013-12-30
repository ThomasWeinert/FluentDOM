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
      $this->_nodes = array();
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
      $result->_document = $this->getDocument();
      $result->_xpath = $this->xpath();
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
     * @return \DOMNode|\DOMElement|NULL
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

    /******************
     * Internal
     *****************/

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
     * check if parameter is a valid callback function
     *
     * @param callback $callback
     * @param boolean $allowGlobalFunctions
     * @param boolean $silent (no InvalidArgumentException)
     * @return boolean
     */
    private function isCallable($callback, $allowGlobalFunctions = FALSE, $silent = TRUE) {
      if ($callback instanceof \Closure) {
        return TRUE;
      } elseif (is_string($callback) &&
        $allowGlobalFunctions &&
        function_exists($callback)) {
        return is_callable($callback);
      } elseif (is_array($callback) &&
        count($callback) == 2 &&
        (is_object($callback[0]) || is_string($callback[0])) &&
        is_string($callback[1])) {
        return is_callable($callback);
      } elseif ($silent) {
        return FALSE;
      } else {
        throw new \InvalidArgumentException('Invalid callback argument');
      }
    }

    /**
     * Execute the callback function for a node and return the new elements
     *
     * @param callable $easySetter
     * @param \DOMNode $node
     * @param integer $index
     * @param string $value
     * @return array
     */
    protected function executeNodeCallback($callback, $node, $index, $value) {
      $contentData = call_user_func($callback, $node, $index, $value);
      if (!empty($contentData)) {
        return $this->getContentNodes($contentData);
      }
      return array();
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
     * @param string|\DOMNode|\DOMNodeList $selector
     * @param \DOMNode $context optional, default value NULL
     * @throws \InvalidArgumentException
     * @return \DOMNodeList
     */
    private function getNodes($selector, \DOMNode $context = NULL, $disallowEmpty = FALSE) {
      if ($this->isNode($selector)) {
        $result = array($selector);
      } elseif (is_string($selector)) {
        $result = $this->xpath()->evaluate($selector, $context, FALSE);
        if (!($result instanceof \DOMNodeList)) {
          throw new \InvalidArgumentException('Given xpath expression did not return an node list.');
        }
        $result = iterator_to_array($result);
      } elseif ($this->isNodeList($selector)) {
        $result = iterator_to_array($selector);
      } else {
        throw new \InvalidArgumentException('Invalid selector');
      }
      if ($disallowEmpty && count($result) < 1) {
        throw new \InvalidArgumentException('Empty node list.');
      }
      return $result;
    }

    /**
     * Convert a given content xml string into and array of nodes
     *
     * @param string $content
     * @param boolean $includeTextNodes
     * @param integer $limit
     * @return array
     */
    private function getContentFragment($content, $includeTextNodes = TRUE, $limit = 0) {
      $result = array();
      $fragment = $this->getDocument()->createDocumentFragment();
      if ($fragment->appendXML($content)) {
        for ($i = $fragment->childNodes->length - 1; $i >= 0; $i--) {
          $element = $fragment->childNodes->item($i);
          if ($element instanceof \DOMElement ||
            ($includeTextNodes && $this->isNode($element))) {
            array_unshift($result, $element);
            $element->parentNode->removeChild($element);
          }
        }
        if ($limit > 0 && count($result) >= $limit) {
          return array_slice($result, 0, $limit);
        }
        return $result;
      } else {
        throw new \UnexpectedValueException('Invalid document fragment');
      }
    }

    /**
     * Convert a given content into and array of nodes
     *
     * @param string|array|\DOMNode|\Traversable $content
     * @param boolean $includeTextNodes
     * @param integer $limit
     * @return array
     */
    private function getContentNodes($content, $includeTextNodes = TRUE, $limit = 0) {
      $result = array();
      if ($content instanceof \DOMElement) {
        $result = array($content);
      } elseif ($includeTextNodes && $this->isNode($content)) {
        $result = array($content);
      } elseif (is_string($content)) {
        $result = $this->getContentFragment($content, $includeTextNodes, $limit);
      } elseif ($this->isNodeList($content)) {
        foreach ($content as $element) {
          if ($element instanceof \DOMElement ||
            ($includeTextNodes && $this->isNode($element))) {
            $result[] = $element;
            if ($limit > 0 && count($result) >= $limit) {
              break;
            }
          }
        }
      } else {
        throw new \InvalidArgumentException('Invalid content parameter');
      }
      if (empty($result)) {
        throw new \UnexpectedValueException('No element found');
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
     * Convert $content to a DOMElement. If $content contains several elements use the first.
     *
     * @param string|array|\DOMElement|\DOMNodeList|\Traversable $content
     * @return \DOMElement
     */
    private function getContentElement($content) {
      if ($content instanceof \DOMElement) {
        return $content;
      } else {
        $contentNodes = $this->getContentNodes($content, FALSE, 1);
        return $contentNodes[0];
      }
    }

    /**
     * Get the inner xml of a given node or in other words the xml of all children.
     *
     * @param \DOMElement $node
     * @return string
     */
    private function getInnerXml($node) {
      $result = '';
      if ($node instanceof \DOMElement) {
        $dom = $this->getDocument();
        foreach ($node->childNodes as $childNode) {
          if ($this->isNode($childNode)) {
            $result .= $dom->saveXML($childNode);
          }
        }
      } elseif ($node instanceof \DOMText || $node instanceOf \DOMCdataSection) {
        return $node->textContent;
      }
      return $result;
    }

    /**
     * Append to content nodes to the target nodes.
     *
     * @param $targetNode
     * @param $contentNodes
     * @return array new nodes
     */
    private function appendNodesTo($targetNode, $contentNodes) {
      $result = array();
      if ($targetNode instanceof \DOMElement) {
        foreach ($contentNodes as $contentNode) {
          /**
           * @var \DOMNode $contentNode
           */
          if ($this->isNode($contentNode)) {
            $result[] = $targetNode->appendChild($contentNode->cloneNode(TRUE));
          }
        }
      }
      return $result;
    }

    /*********************
     * Core
     ********************/

    /**
     * Use a handler callback to apply a content argument to each node $targetNodes. The content
     * argument can be an easy setter function
     *
     * @param array|\DOMNodeList $targetNodes
     * @param string|array|\DOMNode|\DOMNodeList|\Traversable|callable $content
     * @param callable $handler
     */
    protected function apply($targetNodes, $content, $handler) {
      $result = array();
      $isSetterFunction = FALSE;
      if ($this->isCallable($content)) {
        $isSetterFunction = TRUE;
      } else {
        $contentNodes = $this->getContentNodes($content);
      }
      foreach ($targetNodes as $index => $node) {
        if ($isSetterFunction) {
          $contentData = call_user_func($content, $node, $index, $this->getInnerXml($node));
          if (!empty($contentData)) {
            $contentNodes = $this->getContentNodes($contentData);
          }
        }
        if (!empty($contentNodes)) {
          $resultNodes = call_user_func($handler, $node, $contentNodes);
          if (is_array($resultNodes)) {
            $result = array_merge($result, $resultNodes);
          }
        }
      }
      return $result;
    }

    /**
     * Test that xpath expression matches context and return true/false
     *
     * @param string $expr
     * @param \DOMNode $context optional, default value NULL
     * @return boolean
     */
    protected function matches($expr, \DOMNode $context = NULL) {
      $check = $this->xpath->evaluate($expr, $context);
      if ($check instanceof \DOMNodeList) {
        return $check->length > 0;
      } else {
        return (bool)$check;
      }
    }

    /*********************
     * Traversing
     ********************/

    /**
     * Adds more elements, matched by the given expression, to the set of matched elements.
     *
     * @example add.php Usage Examples: FluentDOM::add()
     * @param string $expr XPath expression
     * @return Query
     */
    public function add($expr, $context = NULL) {
      $result = $this->spawn();
      $result->push($this->_nodes);
      if (isset($context)) {
        $targetNodes = $this->getNodes($context);
        if (!empty($targetNodes)) {
          foreach ($targetNodes as $node) {
            $result->push($this->getNodes($expr, $node));
          }
        }
      } elseif (is_object($expr) ||
                (is_string($expr) && substr(ltrim($expr), 0, 1) == '<')) {
        $result->push($this->getContentNodes($expr));
      } else {
        $result->push($this->find($expr));
      }
      $this->_nodes = $this->unique($this->_nodes);
      return $result;
    }

    /**
     * Add the previous selection to the current selection.
     *
     * @return Query
     */
    public function andSelf() {
      $result = $this->spawn();
      $result->push($this->_nodes);
      $result->push($this->_parent);
      return $result;
    }

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
     * Return the parent FluentDOM/Query object.
     *
     * @return Query|NULL
     */
    public function end() {
      return $this->_parent;
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
     * Insert content after each of the matched elements.
     *
     * @example after.php Usage Example: FluentDOM\Query::after()
     * @param string|array|\DOMNode|\DOMNodeList|\Traversable|callable $content
     * @return Query
     */
    public function after($content) {
      $result = $this->spawn();
      $result->push(
        $this->apply(
          $this->_nodes,
          $content,
          function ($targetNode, $contentNodes) {
            $result = array();
            if (isset($targetNode->parentNode) &&
              !empty($contentNodes)) {
              $beforeNode = $targetNode->nextSibling;
              foreach ($contentNodes as $contentNode) {
                /**
                 * @var \DOMNode $contentNode
                 */
                $result[] = $targetNode->parentNode->insertBefore(
                  $contentNode->cloneNode(TRUE), $beforeNode
                );
              }
            }
            return $result;
          }
        )
      );
      return $result;
    }

   /**
   * Append content to the inside of every matched element.
   *
   * @example append.php Usage Example: FluentDOM::append()
   * @param string|array|\DOMNode|\Traversable|callable $content DOMNode or DOMNodeList or xml fragment string
   * @return Query
   */
    public function append($content) {
      $result = $this->spawn();
      if (empty($this->_nodes) &&
        $this->_useDocumentContext &&
        !isset($this->_document->documentElement)) {
        if ($this->isCallable($content)) {
          $contentNode = $this->getContentElement(
            $this->executeNodeCallback($content, NULL, 0, '')
          );
        } else {
          $contentNode = $this->getContentElement($content);
        }
        $result->push($this->_document->appendChild($contentNode));
      } else {
        $result->push(
          $this->apply(
            $this->_nodes,
            $content,
            function($targetNode, $contentNodes) {
              return $this->appendNodesTo($targetNode, $contentNodes);
            }
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
     * @param string|array|\DOMNode|\DOMNodeList|Query $selector
     * @return Query
     */
    public function appendTo($selector) {
      $result = $this->spawn();
      $targetNodes = $this->getNodes($selector);
      if (!empty($targetNodes)) {
        $result->push(
          $this->apply(
            $targetNodes,
            $this->_nodes,
            function ($targetNode, $contentNodes) {
              return $this->appendNodesTo($targetNode, $contentNodes);
            }
          )
        );
        $this->remove();
      }
      return $result;
    }

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
    /**
     * Removes all matched elements from the DOM.
     *
     * @example remove.php Usage Example: FluentDOM\Query::remove()
     * @param string $expr XPath expression
     * @return Query removed elements
     */
    public function remove($expr = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        if (isset($node->parentNode)) {
          if (empty($expr) || $this->matches($expr, $node)) {
            $result->push($node->parentNode->removeChild($node));
          }
        }
      }
      return $result;
    }

    /****************************
     * Manipulation - Attributes
     ***************************/

    /*************************
     * Manipulation - Classes
     ************************/

    /**
     * Adds the specified class(es) to each of the set of matched elements.
     *
     * @param string|callable $class
     * @return Query
     */
    public function addClass($class) {
      return $this->toggleClass($class, TRUE);
    }

    /**
     * Returns true if the specified class is present on at least one of the set of matched elements.
     *
     * @param string|callable $class
     * @return boolean
     */
    public function hasClass($class) {
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement &&
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
     * @param string|callable $class
     * @return Query
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
     * @param string|callable $class
     * @param NULL|boolean $switch toggle if NULL, add if TRUE, remove if FALSE
     * @return Query
     */
    public function toggleClass($class, $switch = NULL) {
      $isCallback = $this->isCallable($class);
      foreach ($this->_nodes as $index => $node) {
        if ($node instanceof \DOMElement) {
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
}