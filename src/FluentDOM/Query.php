<?php
/**
 * FluentDOM\Query implements a jQuery like replacement for DOMNodeList
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\Query implements a jQuery like replacement for DOMNodeList
   *
   * @property string $contentType Output type - text/xml or text/html
   * @property callable $onPrepareSelector A callback to convert the selector into xpath
   * @property-read integer $length The amount of elements found by selector.
   * @property-read Document|\DOMDocument $document Internal DOMDocument object
   * @property-read \DOMXPath $xpath Internal XPath object
   * @property Query\Attributes $attr
   * @property Query\Data $data
   * @property Query\Css $css
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
     * @var array
     */
    private $_namespaces = [];

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
     * A list of loaders for different data sources
     * @var Loadable $loaders
     */
    private $_loaders = NULL;

    /**
     * A callback used to convert the selector to xpath before use
     *
     * @var callable
     */
    private $_onPrepareSelector = NULL;

    /**
     * @param mixed $source
     * @param null|string $contentType
     */
    public function __construct($source = NULL, $contentType = NULL) {
      if (isset($source)) {
        $this->load($source, $contentType);
      } elseif (isset($contentType)) {
        $this->setContentType($contentType);
      }
    }

    /**
     * Load a $source. The type of the source depends on the loaders. If no explicit loaders are set
     * FluentDOM\Query will use a set of default loaders for xml/html and DOM.
     *
     * @param mixed $source
     * @param string $contentType optional, default value 'text/xml'
     * @throws \InvalidArgumentException
     * @return $this
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
      } elseif ($this->loaders()->supports($contentType)) {
        $dom = $this->loaders()->load($source, $contentType);
      }
      if ($dom instanceof \DOMDocument) {
        $this->_document = $dom;
        $this->setContentType($contentType, TRUE);
        unset($this->_xpath);
        $this->applyNamespaces();
        return $this;
      } else {
        throw new \InvalidArgumentException(
          "Can not load: ".(is_object($source) ? get_class($source) : gettype($source))
        );
      }
    }

    /**
     * Set the loaders list.
     *
     * @param Loadable|array|\Traversable $loaders
     * @throws \InvalidArgumentException
     * @return Loadable
     */
    public function loaders($loaders = NULL) {
      if (isset($loaders)) {
        if ($loaders instanceOf Loadable) {
          $this->_loaders = $loaders;
        } elseif (is_array($loaders) || $loaders instanceOf \Traversable) {
          $this->_loaders = new Loaders($loaders);
        } else {
          throw new \InvalidArgumentException(
            "Invalid loader(s) argument."
          );
        }
      } elseif (NULL === $this->_loaders) {
        $this->_loaders = new Loaders(
          [
            new Loader\XmlFile(),
            new Loader\XmlString(),
            new Loader\HtmlFile(),
            new Loader\HtmlString(),
            new Loader\JsonString()
          ]
        );
      }
      return $this->_loaders;
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
     * @return \DOMElement|\DOMNode
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
        $this->_xpath = new Xpath($this->getDocument());
        $this->applyNamespaces();
        return $this->_xpath;
      }
    }

    /**
     * apply stored namespaces to attached document or xpath object
     */
    private function applyNamespaces() {
      $dom = $this->getDocument();
      if ($dom instanceof Document) {
        foreach ($this->_namespaces as $prefix => $namespace) {
          $dom->registerNamespace($prefix, $namespace);
        }
      } elseif (isset($this->_xpath)) {
        foreach ($this->_namespaces as $prefix => $namespace) {
          $this->_xpath->registerNamespace($prefix, $namespace);
        }
      }
    }

    /**
     * Create a new instance of the same class with $this as the parent. This is used for the chaining.
     *
     * @param \Traversable|\DOMNode|Query $elements
     * @return Query
     */
    public function spawn($elements = NULL) {
      $result = clone $this;
      $result->_parent = $this;
      $result->_document = $this->getDocument();
      $result->_xpath = $this->xpath();
      $result->_nodes = array();
      if (isset($elements)) {
        $result->push($elements);
      }
      return $result;
    }

    /**
     * Push new element(s) an the internal element list
     *
     * @param \DOMNode|\Traversable|array $elements
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

    /**
     * Register a namespace for selectors/expressions
     *
     * @param string $prefix
     * @param string $namespace
     */
    public function registerNamespace($prefix, $namespace) {
      $this->_namespaces[$prefix] = $namespace;
      $dom = $this->getDocument();
      if ($dom instanceOf Document) {
        $dom->registerNamespace($prefix, $namespace);
      } elseif (isset($this->_xpath)) {
        $this->_xpath->registerNamespace($prefix, $namespace);
      }
    }

    /**************
     * Interfaces
     *************/

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
     * @return \DOMElement|\DOMNode|NULL
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
      case 'attr' :
      case 'contentType' :
      case 'css' :
      case 'data' :
      case 'length' :
      case 'xpath' :
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
     * @throws \UnexpectedValueException
     * @return mixed
     */
    public function __get($name) {
      switch ($name) {
      case 'attr' :
        return new Query\Attributes($this);
      case 'css' :
        return new Query\Css($this, $this->attr('style'));
      case 'data' :
        if (count($this->_nodes) > 0 &&
          $this->_nodes[0] instanceof \DOMElement) {
          return new Query\Data($this->_nodes[0]);
        } else {
          throw new \UnexpectedValueException(
            'UnexpectedValueException: first selected node is no element.'
          );
        }
      case 'contentType' :
        return $this->_contentType;
      case 'document' :
        return $this->getDocument();
      case 'length' :
        return count($this->_nodes);
      case 'xpath' :
        return $this->xpath();
      case 'onPrepareSelector' :
        return $this->_onPrepareSelector;
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
      case 'attr' :
        if ($value instanceOf Query\Attributes) {
          $this->attr($value->toArray());
        } else {
          $this->attr($value);
        }
        break;
      case 'contentType' :
        $this->setContentType($value);
        break;
      case 'css' :
        $this->css($value);
        break;
      case 'onPrepareSelector' :
        if ($this->isCallable($value, TRUE, FALSE)) {
          $this->_onPrepareSelector = $value;
        }
        break;
      case 'data' :
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
     * of the dynamic properties
     *
     * @param string $name
     * @throws \BadMethodCallException
     */
    public function __unset($name) {
      switch ($name) {
      case 'attr' :
      case 'contentType' :
      case 'data' :
      case 'document' :
      case 'length' :
      case 'xpath' :
        throw new \BadMethodCallException(
          sprintf(
            'Can not unset property %s::$%s',
            get_class($this),
            $name
          )
        );
      }
      throw new \BadMethodCallException(
        sprintf(
          'Can not unset non existing property %s::$%s',
          get_class($this),
          $name
        )
      );
    }

    /**
     * declaring an empty() or clone() method will crash the parser so we use some magic
     *
     * @param string $name
     * @param array $arguments
     * @throws \BadMethodCallException
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
     * Utility
     *****************/

    /**
     * Check if the DOMNode is DOMElement or DOMText with content
     *
     * @param \DOMNode $node
     * @param boolean $ignoreTextNodes
     * @return boolean
     */
    public function isNode($node, $ignoreTextNodes = FALSE) {
      if ($node instanceof \DOMElement) {
        return TRUE;
      } elseif (!$ignoreTextNodes) {
        if ($node instanceof \DOMCdataSection) {
          return TRUE;
        } elseif ($node instanceof \DOMText &&
                  !$node->isWhitespaceInElementContent()) {
          return TRUE;
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
    public function isNodeList($elements) {
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
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function isCallable($callback, $allowGlobalFunctions = FALSE, $silent = TRUE) {
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

    /******************
     * Internal
     *****************/

    /**
     * Setter for Query::_contentType property
     *
     * @param string $value
     * @param bool $silentFallback
     * @throws \Exception
     * @throws \UnexpectedValueException
     */
    private function setContentType($value, $silentFallback = FALSE) {
      try {
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
      } catch (\UnexpectedValueException $e) {
        if ($silentFallback) {
          $newContentType = 'text/xml';
        } else {
          throw $e;
        }
      }
      if ($this->_contentType != $newContentType) {
        $this->_contentType = $newContentType;
        if (isset($this->_parent)) {
          $this->_parent->contentType = $newContentType;
        }
      }
    }

    /**
     * Get the associated DOM, create one if here isn't one yet.
     *
     * @return \DOMDocument|Document
     */
    private function getDocument() {
      if (!($this->_document instanceof \DOMDocument)) {
        $this->_document = new Document();
        $this->applyNamespaces();
      }
      return $this->_document;
    }

    /**
     * Match selector against context and return matched elements.
     *
     * @param string|\DOMNode|\DOMNodeList $selector
     * @param \DOMNode $context optional, default value NULL
     * @throws \InvalidArgumentException
     * @return \DOMNodeList
     */
    private function getNodes($selector, \DOMNode $context = NULL) {
      if ($this->isNode($selector)) {
        $result = array($selector);
      } elseif (is_string($selector)) {
        if (isset($this->_onPrepareSelector)) {
          $selector = call_user_func($this->_onPrepareSelector, $selector);
        }
        $result = $this->xpath()->evaluate($selector, $context, FALSE);
        if (!($result instanceof \Traversable)) {
          throw new \InvalidArgumentException('Given selector did not return an node list.');
        }
        $result = iterator_to_array($result);
      } elseif ($this->isNodeList($selector)) {
        $result = is_array($selector) ? $selector : iterator_to_array($selector);
      } else {
        throw new \InvalidArgumentException('Invalid selector');
      }
      return $result;
    }

    /**
     * Convert a given content xml string into and array of nodes
     *
     * @param string $content
     * @param boolean $includeTextNodes
     * @param integer $limit
     * @throws \UnexpectedValueException
     * @return array
     */
    private function getContentFragment($content, $includeTextNodes = TRUE, $limit = 0) {
      $result = array();
      $fragment = $this->getDocument()->createDocumentFragment();
      if (
        (is_string($content) || method_exists($content, '__toString')) &&
        $fragment->appendXML($content)
      ) {
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
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
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
      $contentNodes = $this->getContentNodes($content, FALSE, 1);
      return $contentNodes[0];
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

    private function uniqueSortNodes() {
      $this->_nodes = $this->unique($this->_nodes);
    }

    /**
     * Wrap $content around a set of elements
     *
     * @param array $elements
     * @param string|array|\DOMNode|\Traversable|callable $content
     * @return Query
     */
    private function wrapNodes($elements, $content) {
      $result = array();
      $wrapperTemplate = NULL;
      $isCallback = $this->isCallable($content, FALSE, TRUE);
      if (!$isCallback) {
        $wrapperTemplate = $this->getContentElement($content);
      }
      $simple = FALSE;
      foreach ($elements as $index => $node) {
        if ($isCallback) {
          $wrapperTemplate = NULL;
          $wrapContent = call_user_func($content, $node, $index);
          if (!empty($wrapContent)) {
            $wrapperTemplate = $this->getContentElement($wrapContent);
          }
        }
        if ($wrapperTemplate instanceof \DOMElement) {
          $wrapper = $wrapperTemplate->cloneNode(TRUE);
          $targets = NULL;
          if (!$simple) {
            $targets = $this->xpath()->evaluate('.//*[count(*) = 0]', $wrapper);
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
     * Append to content nodes to the target nodes.
     *
     * @param $targetNode
     * @param $contentNodes
     * @return array new nodes
     */
    private function appendChildren($targetNode, $contentNodes) {
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

    /**
     * Insert nodes into target as first childs.
     *
     * @param \DOMNode $targetNode
     * @param array|\DOMNodeList|Query $contentNodes
     * @return array
     */
    private function insertChildrenBefore($targetNode, $contentNodes) {
      $result = array();
      if ($targetNode instanceof \DOMElement) {
        $firstChild = $targetNode->hasChildNodes() ? $targetNode->childNodes->item(0) : NULL;
        foreach ($contentNodes as $contentNode) {
          /**
           * @var \DOMNode $contentNode
           */
          if ($this->isNode($contentNode)) {
            $result[] = $targetNode->insertBefore(
              $contentNode->cloneNode(TRUE),
              $firstChild
            );
          }
        }
      }
      return $result;
    }

    /**
     * Insert nodes after the target node.
     * @param \DOMNode $targetNode
     * @param array|\DOMNodeList|Query $contentNodes
     * @return array
     */
    public static function insertNodesAfter($targetNode, $contentNodes) {
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

    /**
     * Insert nodes before the target node.
     * @param \DOMNode $targetNode
     * @param array|\DOMNodeList|Query $contentNodes
     * @return array
     */
    private function insertNodesBefore($targetNode, $contentNodes) {
      $result = array();
      if (isset($targetNode->parentNode) &&
        !empty($contentNodes)) {
        foreach ($contentNodes as $contentNode) {
          /**
           * @var \DOMNode $contentNode
           */
          $result[] = $targetNode->parentNode->insertBefore(
            $contentNode->cloneNode(TRUE), $targetNode
          );
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
     * @return array
     */
    public function apply($targetNodes, $content, $handler) {
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
     * Search for a given element from among the matched elements.
     *
     * @param NULL|string|\DOMNode|\Traversable $selector
     * @return integer
     */
    public function index($selector = NULL) {
      if (count($this->_nodes) > 0) {
        if (is_null($selector)) {
          $counter = -1;
          $targetNode = $this->_nodes[0];
          $nodeList = $this->getNodes('preceding-sibling::node()', $targetNode);
          foreach ($nodeList as $node) {
            if ($this->isNode($node)) {
              $counter++;
            }
          }
          return $counter + 1;
        } elseif (is_string($selector)) {
          foreach ($this->_nodes as $index => $node) {
            if ($this->matches($selector, $node)) {
              return $index;
            }
          }
        } else {
          $targetNode = $this->getContentElement($selector);
          foreach ($this->_nodes as $index => $node) {
            /**
             * @var \DOMNode $node
             */
            if ($node->isSameNode($targetNode)) {
              return $index;
            }
          }
        }
      }
      return -1;
    }

    /**
     * Retrieve the matched DOM nodes in an array.
     *
     * @return array
     */
    public function toArray() {
      return $this->_nodes;
    }

    /**
     * Test that selector matches context and return true/false
     *
     * @param string $selector
     * @param \DOMNode $context optional, default value NULL
     * @return boolean
     */
    public function matches($selector, \DOMNode $context = NULL) {
      if (isset($this->_onPrepareSelector)) {
        $selector = call_user_func($this->_onPrepareSelector, $selector);
      }
      $check = $this->xpath->evaluate($selector, $context);
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
     * @param string $selector selector
     * @param array|\Traversable $context
     * @return Query
     */
    public function add($selector, $context = NULL) {
      $result = $this->spawn();
      $result->push($this->_nodes);
      if (isset($context)) {
        $targetNodes = $this->getNodes($context);
        if (!empty($targetNodes)) {
          foreach ($targetNodes as $node) {
            $result->push($this->getNodes($selector, $node));
          }
        }
      } elseif (is_object($selector) ||
                (is_string($selector) && substr(ltrim($selector), 0, 1) == '<')) {
        $result->push($this->getContentNodes($selector));
      } else {
        $result->push($this->find($selector));
      }
      $this->uniqueSortNodes();
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
     * Get a set of elements containing of the unique immediate
     * child nodes including only elements (not text nodes) of each
     * of the matched set of elements.
     *
     * @example children.php Usage Examples: FluentDOM\Query::children()
     * @param string $selector selector
     * @return Query
     */
    public function children($selector = NULL) {
      $nodes = array();
      foreach ($this->_nodes as $node) {
        if (empty($selector)) {
          $nodes = iterator_to_array($node->childNodes);
        } else {
          foreach ($node->childNodes as $childNode) {
            if ($this->matches($selector, $childNode)) {
              $nodes[] = $childNode;
            }
          }
        }
      }
      $result = $this->spawn();
      $result->push($this->unique($nodes), TRUE);
      return $result;
    }

    /**
     * Get a set of elements containing the closest parent element that matches the specified
     * selector, the starting element included.
     *
     * @example closest.php Usage Example: FluentDOM\Query::closest()
     * @param string $selector selector
     * @param array|\Traversable $context
     * @return Query
     */
    public function closest($selector, $context = NULL) {
      $result = $this->spawn();
      if (is_null($context)) {
        $context = $this->_nodes;
      } else {
        $context = $this->getNodes($context);
      }
      foreach ($context as $node) {
        while (isset($node)) {
          if ($this->matches($selector, $node)) {
            $result->push($node);
            break;
          }
          $node = $node->parentNode;
        }
      }
      return $result;
    }

    /**
     * Get a set of elements containing all of the unique immediate
     * child nodes including elements and text nodes of each of the matched set of elements.
     *
     * @return Query
     */
    public function contents() {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $result->push($node->childNodes, FALSE);
      }
      $this->uniqueSortNodes();
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
     * Return the parent FluentDOM\Query object.
     *
     * @return Query|NULL
     */
    public function end() {
      if ($this->_parent instanceof Query) {
        return $this->_parent;
      } else {
        return $this;
      }
    }

    /**
     * Reduce the set of matched elements to a single element.
     *
     * @example eq.php Usage Example: FluentDOM\Query::eq()
     * @param integer $position Element index (start with 0)
     * @return Query
     */
    public function eq($position) {
      $result = $this->spawn();
      if ($position < 0) {
        $position = count($this->_nodes) + $position;
      }
      if (isset($this->_nodes[$position])) {
        $result->push($this->_nodes[$position]);
      }
      return $result;
    }

    /**
     * Removes all elements from the set of matched elements that do not match
     * the specified expression(s).
     *
     * @example filter-expr.php Usage Example: FluentDOM\Query::filter() with selector
     * @example filter-fn.php Usage Example: FluentDOM\Query::filter() with Closure
     * @param string|callable $selector selector or callback function
     * @return Query
     */
    public function filter($selector) {
      $result = $this->spawn();
      foreach ($this->_nodes as $index => $node) {
        $check = TRUE;
        if (is_string($selector)) {
          $check = $this->matches($selector, $node, $index);
        } elseif (is_callable($selector)) {
          $check = call_user_func($selector, $node, $index);
        }
        if ($check) {
          $result->push($node);
        }
      }
      return $result;
    }

    /**
     * Searches for descendant elements that match the specified expression.
     *
     * @example find.php Usage Example: FluentDOM::find()
     * @param string $selector selector
     * @param boolean $useDocumentContext ignore current node list
     * @return Query
     */
    public function find($selector, $useDocumentContext = FALSE) {
      $result = $this->spawn();
      if ($useDocumentContext ||
        $this->_useDocumentContext) {
        $result->push($this->getNodes($selector));
      } else {
        foreach ($this->_nodes as $contextNode) {
          $result->push($this->getNodes($selector, $contextNode));
        }
      }
      return $result;
    }

    /**
     * Get a set of elements containing only the first of the currently selected elements.
     *
     * @return Query
     */
    public function first() {
      return $this->eq(0);
    }

    /**
     * Retrieve the matched DOM elements in an array. A negative position will be counted from the end.
     *
     * @param integer|NULL optional offset of a single element to get.
     * @return array
     */
    public function get($position = NULL) {
      if (!isset($position)) {
        return $this->_nodes;
      }
      if ($position < 0) {
        $position = count($this->_nodes) + $position;
      }
      if (isset($this->_nodes[$position])) {
        return array($this->_nodes[$position]);
      } else {
        return array();
      }
    }

    /**
     * Reduce the set of matched elements to those that have
     * a descendant that matches the selector or DOM element.
     *
     * @param string|\DOMNode $selector selector or DOMNode
     * @return Query
     */
    public function has($selector) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement &&
          $node->hasChildNodes()) {
          foreach ($node->childNodes as $childNode) {
            if ($selector instanceof \DOMNode) {
              if ($selector === $childNode) {
                $result->push($node);
                return $result;
              }
            } elseif ($this->matches($selector, $childNode)) {
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
     * @example is.php Usage Example: FluentDOM\Query::is()
     * @param string $selector selector
     * @return boolean
     */
    public function is($selector) {
      foreach ($this->_nodes as $node) {
        return $this->matches($selector, $node);
      }
      return FALSE;
    }

    /**
     * Get a set of elements containing only the last of the currently selected elements.
     *
     * @return Query
     */
    public function last() {
      return $this->eq(-1);
    }

    /**
     * Translate a set of elements in the FluentDOM\Query object into
     * another set of values in an array (which may, or may not contain elements).
     *
     * If the callback function returns an array each element of the array will be added to the
     * result array. All other variable types are put directly into the result array.
     *
     * @example map.php Usage Example: FluentDOM\Query::map()
     * @param callable $function
     * @return array
     */
    public function map(Callable $function) {
      $result = array();
      foreach ($this->_nodes as $index => $node) {
        $mapped = call_user_func($function, $node, $index);
        if ($mapped === NULL) {
          continue;
        } elseif ($mapped instanceof \Traversable || is_array($mapped)) {
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
     * @example not.php Usage Example: FluentDOM\Query::not()
     * @param string|callback $selector selector or callback function
     * @return Query
     */
    public function not($selector) {
      $result = $this->spawn();
      foreach ($this->_nodes as $index => $node) {
        $check = FALSE;
        if (is_string($selector)) {
          $check = $this->matches($selector, $node, $index);
        } elseif ($this->isCallable($selector, TRUE, FALSE)) {
          $check = call_user_func($selector, $node, $index);
        }
        if (!$check) {
          $result->push($node);
        }
      }
      return $result;
    }

    /**
     * Get a set of elements containing the unique next siblings of each of the
     * given set of elements.
     *
     * @example next.php Usage Example: FluentDOM\Query::next()
     * @param string $selector
     * @return Query
     */
    public function next($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $next = $node->nextSibling;
        while ($next instanceof \DOMNode && !$this->isNode($next)) {
          $next = $next->nextSibling;
        }
        if (!empty($next)) {
          if (empty($selector) || $this->matches($selector, $next)) {
            $result->push($next);
          }
        }
      }
      $this->uniqueSortNodes();
      return $result;
    }

    /**
     * Find all sibling elements after the current element.
     *
     * @example nextAll.php Usage Example: FluentDOM\Query::nextAll()
     * @param string $selector selector
     * @return Query
     */
    public function nextAll($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $next = $node->nextSibling;
        while ($next instanceof \DOMNode) {
          if ($this->isNode($next)) {
            if (empty($selector) || $this->matches($selector, $next)) {
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
     * @param string $selector selector
     * @return Query
     */
    public function nextUntil($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $next = $node->nextSibling;
        while ($next instanceof \DOMNode) {
          if ($this->isNode($next)) {
            if (isset($selector) && $this->matches($selector, $next)) {
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
     * @example parent.php Usage Example: FluentDOM\Query::parent()
     * @return Query
     */
    public function parent() {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        if (isset($node->parentNode)) {
          $result->push($node->parentNode);
        }
      }
      $this->uniqueSortNodes();
      return $result;
    }

    /**
     * Get the ancestors of each element in the current set of matched elements,
     * optionally filtered by a selector.
     *
     * @example parents.php Usage Example: FluentDOM\Query::parents()
     * @param string $selector selector
     * @return Query
     */
    public function parents($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $parents = $this->xpath()->evaluate('ancestor::*', $node);
        for ($i = $parents->length - 1; $i >= 0; --$i) {
          $parentNode = $parents->item($i);
          if (empty($selector) || $this->matches($selector, $parentNode)) {
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
     * @param string $selector selector
     * @return Query
     */
    public function parentsUntil($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $parents = $this->xpath()->evaluate('ancestor::*', $node);
        for ($i = $parents->length - 1; $i >= 0; --$i) {
          $parentNode = $parents->item($i);
          if (!empty($selector) && $this->matches($selector, $parentNode)) {
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
     * @example prev.php Usage Example: FluentDOM\Query::prev()
     * @param string $selector selector
     * @return Query
     */
    public function prev($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $previous = $node->previousSibling;
        while ($previous instanceof \DOMNode && !$this->isNode($previous)) {
          $previous = $previous->previousSibling;
        }
        if (!empty($previous)) {
          if (empty($selector) || $this->matches($selector, $previous)) {
            $result->push($previous);
          }
        }
      }
      $result->uniqueSortNodes();
      return $result;
    }

    /**
     * Find all sibling elements in front of the current element.
     *
     * @example prevAll.php Usage Example: FluentDOM\Query::prevAll()
     * @param string $selector selector
     * @return Query
     */
    public function prevAll($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $previous = $node->previousSibling;
        while ($previous instanceof \DOMNode) {
          if ($this->isNode($previous)) {
            if (empty($selector) || $this->matches($selector, $previous)) {
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
     * @param string $selector selector
     * @return Query
     */
    public function prevUntil($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $previous = $node->previousSibling;
        while ($previous instanceof \DOMNode) {
          if (isset($selector) && $this->isNode($previous)) {
            if ($this->matches($selector, $previous)) {
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
     * Reverse the order of the matched elements.
     *
     * @return Query
     */
    public function reverse() {
      $result = $this->spawn();
      $result->push(array_reverse($this->_nodes));
      return $result;
    }

    /**
     * Get a set of elements containing all of the unique siblings of each of the
     * matched set of elements.
     *
     * @example siblings.php Usage Example: FluentDOM\Query::siblings()
     * @param string $selector selector
     * @return Query
     */
    public function siblings($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        if (isset($node->parentNode)) {
          foreach ($node->parentNode->childNodes as $childNode) {
            if ($this->isNode($childNode) &&
              $childNode !== $node) {
              if (empty($selector) || $this->matches($selector, $childNode)) {
                $result->push($childNode);
              }
            }
          }
        }
      }
      $this->uniqueSortNodes();
      return $result;
    }

    /**
     * Selects a subset of the matched elements.
     *
     * @example slice.php Usage Example: FluentDOM\Query::slice()
     * @param integer $start
     * @param integer $end
     * @return Query
     */
    public function slice($start, $end = NULL) {
      $result = $this->spawn();
      if ($end === NULL) {
        $result->push(array_slice($this->_nodes, $start));
      } elseif ($end < 0) {
        $result->push(array_slice($this->_nodes, $start, $end));
      } elseif ($end > $start) {
        $result->push(array_slice($this->_nodes, $start, $end - $start));
      } else {
        $result->push(array_slice($this->_nodes, $end, $start - $end));
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
            return $this->insertNodesAfter($targetNode, $contentNodes);
          }
        )
      );
      return $result;
    }

   /**
   * Append content to the inside of every matched element.
   *
   * @example append.php Usage Example: FluentDOM\Query::append()
   * @param string|array|\DOMNode|\Traversable|callable $content DOMNode or DOMNodeList or xml fragment string
   * @return Query
   */
    public function append($content) {
      $result = $this->spawn();
      if (empty($this->_nodes) &&
        $this->_useDocumentContext &&
        !isset($this->_document->documentElement)) {
        if ($this->isCallable($content)) {
          $contentNode = $this->getContentElement($content(NULL, 0, ''));
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
              return $this->appendChildren($targetNode, $contentNodes);
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
     * @example appendTo.php Usage Example: FluentDOM\Query::appendTo()
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
              return $this->appendChildren($targetNode, $contentNodes);
            }
          )
        );
        $this->remove();
      }
      return $result;
    }

    /**
     * Insert content before each of the matched elements.
     *
     * @example before.php Usage Example: FluentDOM\Query::before()
     * @param string|array|\DOMNode|\Traversable|callable $content
     * @return Query
     */
    public function before($content) {
      $result = $this->spawn();
      $result->push(
        $this->apply(
          $this->_nodes,
          $content,
          function($targetNode, $contentNodes) {
            return $this->insertNodesBefore($targetNode, $contentNodes);
          }
        )
      );
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
     * Insert all of the matched elements after another, specified, set of elements.
     *
     * @example insertAfter.php Usage Example: FluentDOM\Query::insertAfter()
     * @param string|array|\DOMNode|\Traversable $selector
     * @return Query
     */
    public function insertAfter($selector) {
      $result = $this->spawn();
      $targetNodes = $this->getNodes($selector);
      if (!empty($targetNodes)) {
        $result->push(
          $this->apply(
            $targetNodes,
            $this->_nodes,
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
        $this->remove();
      }
      return $result;
    }

    /**
     * Insert all of the matched elements before another, specified, set of elements.
     *
     * @example insertBefore.php Usage Example: FluentDOM\Query::insertBefore()
     * @param string|array|\DOMNode|\Traversable $selector
     * @return Query
     */
    public function insertBefore($selector) {
      $result = $this->spawn();
      $targetNodes = $this->getNodes($selector);
      if (!empty($targetNodes)) {
        $result->push(
          $this->apply(
            $targetNodes,
            $this->_nodes,
            function ($targetNode, $contentNodes) {
              $result = array();
              if (isset($targetNode->parentNode) &&
                !empty($contentNodes)) {
                foreach ($contentNodes as $contentNode) {
                  /**
                   * @var \DOMNode $contentNode
                   */
                  $result[] = $targetNode->parentNode->insertBefore(
                    $contentNode->cloneNode(TRUE), $targetNode
                  );
                }
              }
              return $result;
            }
          )
        );
        $this->remove();
      }
      return $result;
    }

    /**
     * Prepend content to the inside of every matched element.
     *
     * @example prepend.php Usage Example: FluentDOM\Query::prepend()
     * @param string|array|\DOMNode|\Traversable $content
     * @return Query
     */
    public function prepend($content) {
      $result = $this->spawn();
      $result->push(
        $this->apply(
          $this->_nodes,
          $content,
          function ($targetNode, $contentNodes) {
            return $this->insertChildrenBefore($targetNode, $contentNodes);
          }
        )
      );
      return $result;
    }

    /**
     * Prepend all of the matched elements to another, specified, set of elements.
     * Returns all of the inserted elements.
     *
     * @example prependTo.php Usage Example: FluentDOM\Query::prependTo()
     * @param string|array|\DOMNode|\DOMNodeList|Query $selector
     * @return Query list of all new elements
     */
    public function prependTo($selector) {
      $result = $this->spawn();
      $targetNodes = $this->getNodes($selector);
      if (!empty($targetNodes)) {
        $result->push(
          $this->apply(
            $targetNodes,
            $this->_nodes,
            function($targetNode, $contentNodes) {
              return $this->insertChildrenBefore($targetNode, $contentNodes);
            }
          )
        );
        $this->remove();
      }
      return $result;
    }

    /**
     * Replaces the elements matched by the specified selector with the matched elements.
     *
     * @example replaceAll.php Usage Example: FluentDOM\Query::replaceAll()
     * @param string|array|\DOMNode|\Traversable $selector
     * @return Query
     */
    public function replaceAll($selector) {
      $result = $this->spawn();
      $targetNodes = $this->getNodes($selector);
      if (!empty($targetNodes)) {
        $this->apply(
          $targetNodes,
          $this->_nodes,
          function($targetNode, $contentNodes) {
            return $this->insertNodesBefore($targetNode, $contentNodes);
          }
        );
        $target = $this->spawn();
        $target->push($targetNodes);
        $target->remove();
      }
      $this->remove();
      return $result;
    }

    /**
     * Replaces all matched elements with the specified HTML or DOM elements.
     * This returns the element that was just replaced,
     * which has been removed from the DOM.
     *
     * @example replaceWith.php Usage Example: FluentDOM\Query::replaceWith()
     * @param string|array|\DOMNode|\Traversable|callable $content
     * @return Query
     */
    public function replaceWith($content) {
      $this->apply(
        $this->_nodes,
        $content,
        function($targetNode, $contentNodes) {
          return $this->insertNodesBefore($targetNode, $contentNodes);
        }
      );
      $this->remove();
      return $this;
    }

    /**
     * Removes all matched elements from the DOM.
     *
     * @example remove.php Usage Example: FluentDOM\Query::remove()
     * @param string $selector selector
     * @return Query removed elements
     */
    public function remove($selector = NULL) {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        if (isset($node->parentNode)) {
          if (empty($selector) || $this->matches($selector, $node)) {
            $result->push($node->parentNode->removeChild($node));
          }
        }
      }
      return $result;
    }

    /**
     * Get the combined text contents of all matched elements or
     * set the text contents of all matched elements.
     *
     * @example text.php Usage Example: FluentDOM\Query::text()
     * @param string|callable $text
     * @return string|Query
     */
    public function text($text = NULL) {
      if (isset($text)) {
        $isCallback = $this->isCallable($text, FALSE, TRUE);
        foreach ($this->_nodes as $index => $node) {
          if ($isCallback) {
            $node->nodeValue = call_user_func($text, $node, $index, $node->nodeValue);
          } else {
            $node->nodeValue = $text;
          }
        }
        return $this;
      } else {
        $result = '';
        foreach ($this->_nodes as $node) {
          $result .= $node->textContent;
        }
        return $result;
      }
    }

    /**
     * Wrap each matched element with the specified content.
     *
     * If $content contains several elements the first one is used
     *
     * @example wrap.php Usage Example: FluentDOM\Query::wrap()
     * @param string|array|\DOMNode|\Traversable|callable $content
     * @return Query
     */
    public function wrap($content) {
      $result = $this->spawn();
      $result->push($this->wrapNodes($this->_nodes, $content));
      return $result;
    }

    /**
     * Wrap al matched elements with the specified content
     *
     * If the matched elements are not siblings, wrap each group of siblings.
     *
     * @example wrapAll.php Usage Example: FluentDOM::wrapAll()
     * @param string|array|\DOMNode|\Traversable $content
     * @return Query
     */
    public function wrapAll($content) {
      $result = $this->spawn();
      $current = NULL;
      $counter = 0;
      $groups = array();
      //group elements by previous node - ignore whitespace text nodes
      foreach ($this->_nodes as $node) {
        $previous = $node->previousSibling;
        while ($previous instanceof \DOMText && $previous->isWhitespaceInElementContent()) {
          $previous = $previous->previousSibling;
        }
        if ($previous !== $current) {
          $counter++;
        }
        $groups[$counter][] = $node;
        $current = $node;
      }
      if (count($groups) > 0) {
        $wrapperTemplate = $this->getContentElement($content);
        $simple = FALSE;
        foreach ($groups as $group) {
          if (isset($group[0])) {
            $node = $group[0];
            $wrapper = $wrapperTemplate->cloneNode(TRUE);
            $targets = NULL;
            if (!$simple) {
              $targets = $this->xpath()->evaluate('.//*[count(*) = 0]', $wrapper);
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
     * @example wrapInner.php Usage Example: FluentDOM\Query::wrapInner()
     * @param string|array|\DOMNode|\Traversable $content
     * @return Query
     */
    public function wrapInner($content) {
      $result = $this->spawn();
      $elements = array();
      foreach ($this->_nodes as $node) {
        foreach ($node->childNodes as $childNode) {
          if ($this->isNode($childNode)) {
            $elements[] = $childNode;
          }
        }
      }
      $result->push($this->wrapNodes($elements, $content));
      return $result;
    }

    /**
     * Get xml contents of the first matched element or set the
     * xml contents of all selected element nodes.
     *
     * @example xml.php Usage Example: FluentDOM::xml()
     * @param string|callable|NULL $xml XML fragment
     * @return string|self
     */
    public function xml($xml = NULL) {
      if (isset($xml)) {
        $isCallback = $this->isCallable($xml, FALSE, TRUE);
        if ($isCallback) {
          foreach ($this->_nodes as $index => $node) {
            $xmlString = call_user_func(
              $xml,
              $node,
              $index,
              $this->getInnerXml($node)
            );
            $node->nodeValue = '';
            if (!empty($xmlString)) {
              $fragment = $this->getContentFragment($xmlString, TRUE);
              foreach ($fragment as $contentNode) {
                /**
                 * @var \DOMNode $node
                 * @var \DOMNode $contentNode
                 */
                $node->appendChild($contentNode->cloneNode(TRUE));
              }
            }
          }
        } else {
          if (!empty($xml)) {
            $fragment = $this->getContentFragment($xml, TRUE);
          } else {
            $fragment = array();
          }
          foreach ($this->_nodes as $node) {
            $node->nodeValue = '';
            foreach ($fragment as $contentNode) {
              /**
               * @var \DOMNode $node
               * @var \DOMNode $contentNode
               */
              $node->appendChild($contentNode->cloneNode(TRUE));
            }
          }
        }
        return $this;
      } else {
        if (isset($this->_nodes[0])) {
          return $this->getInnerXml($this->_nodes[0]);
        }
        return '';
      }
    }

    /**
     * Get the first matched node as XML or replace all
     * matched nodes with the provided fragment.
     *
     * @param string|callable|NULL $xml
     * @return string|self
     */
    function outerXml($xml = NULL) {
      if (isset($xml)) {
        $isCallback = $this->isCallable($xml, FALSE, TRUE);
        if ($isCallback) {
          /** @var \DOMNode $node */
          foreach ($this->_nodes as $index => $node) {
            $xmlString = call_user_func(
              $xml,
              $node,
              $index,
              $this->getDocument()->saveXML($node)
            );
            if (!empty($xmlString)) {
              $fragment = $this->getContentFragment($xmlString, TRUE);
              /** @var \DOMNode $contentNode */
              foreach ($fragment as $contentNode) {
                $node->parentNode->insertBefore(
                  $contentNode->cloneNode(TRUE),
                  $node
                );
              }
            }
            $node->parentNode->removeChild($node);
          }
        } else {
          if (!empty($xml)) {
            $fragment = $this->getContentFragment($xml, TRUE);
          } else {
            $fragment = array();
          }
          /** @var \DOMNode $node */
          foreach ($this->_nodes as $node) {
            /** @var \DOMNode $contentNode */
            foreach ($fragment as $contentNode) {
              $node->parentNode->insertBefore(
                $contentNode->cloneNode(TRUE),
                $node
              );
            }
            $node->parentNode->removeChild($node);
          }
        }
        return $this;
      } else {
        if (isset($this->_nodes[0]) && $this->isNode($this->_nodes[0])) {
          return $this->getDocument()->saveXml($this->_nodes[0]);
        }
        return '';
      }
    }

    /**
     * Get html contents of the first matched element or set the
     * html contents of all selected element nodes.
     *
     * @param string|callable|NULL $html
     * @return string|self
     */
    public function html($html = NULL) {
      if (isset($html)) {
        $isCallback = $this->isCallable($html, FALSE, TRUE);
        if ($isCallback) {
          /** @var \DOMNode $node */
          foreach ($this->_nodes as $index => $node) {
            $htmlString = call_user_func(
              $html,
              $node,
              $index,
              $this->getInnerXml($node)
            );
            $node->nodeValue = '';
            if (!empty($htmlString)) {
              $fragment = $this->getHtmlFragment($htmlString, TRUE);
              /** @var \DOMNode $contentNode */
              foreach ($fragment as $contentNode) {
                $node->appendChild(
                  $this->getDocument()->importNode($contentNode, TRUE)
                );
              }
            }
          }
        } else {
          $fragment = $this->getHtmlFragment($html);
          /** @var \DOMNode $node */
          foreach ($this->_nodes as $node) {
            $node->nodeValue = '';
            /** @var \DOMNode $contentNode */
            foreach ($fragment as $contentNode) {
              $node->appendChild(
                $this->getDocument()->importNode($contentNode, TRUE)
              );
            }
          }
        }
        return $this;
      }
      if (isset($this->_nodes[0]) &&
          $this->isNode($this->_nodes[0], TRUE)) {
        $result = '';
        foreach ($this->_nodes[0]->childNodes as $node) {
          $result .= $this->getDocument()->saveHTML($node);
        }
        return $result;
      }
      return '';
    }

    private function getHtmlFragment($html) {
      $dom = new Document();
      $status = libxml_use_internal_errors(TRUE);
      $dom->loadHtml('<html-fragment>'.$html.'</html-fragment>');
      libxml_clear_errors();
      libxml_use_internal_errors($status);
      return iterator_to_array($dom->xpath()->evaluate('//html-fragment[1]/node()'));
    }

    /****************************
     * Manipulation - Attributes
     ***************************/

    /**
     * Access a property on the first matched element or set the attribute(s) of all matched elements
     *
     * @example attr.php Usage Example: FluentDOM:attr() Read an attribute value.
     * @param string|array $attribute attribute name or attribute list
     * @param string|callable $value function callback($index, $value) or value
     * @return string|Query attribute value or $this
     */
    public function attr($attribute, $value = NULL) {
      if (is_array($attribute) && count($attribute) > 0) {
        //expr is an array of attributes and values - set on each element
        foreach ($attribute as $key => $value) {
          $name = (new QualifiedName($key))->name;
          foreach ($this->_nodes as $node) {
            if ($node instanceof \DOMElement) {
              $node->setAttribute($name, $value);
            }
          }
        }
      } elseif (is_null($value)) {
        //empty value - read attribute from first element in list
        $attribute = (new QualifiedName($attribute))->name;
        if (count($this->_nodes) > 0) {
          $node = $this->_nodes[0];
          if ($node instanceof \DOMElement && $node->hasAttribute($attribute)) {
            return $node->getAttribute($attribute);
          }
        }
        return NULL;
      } elseif ($this->isCallable($value)) {
        //value is function callback - execute it and set result on each element
        $attribute = (new QualifiedName($attribute))->name;
        foreach ($this->_nodes as $index => $node) {
          if ($node instanceof \DOMElement) {
            $node->setAttribute(
              $attribute,
              call_user_func($value, $node, $index, $node->getAttribute($attribute))
            );
          }
        }
      } else {
        // set attribute value of each element
        $attribute = (new QualifiedName($attribute))->name;
        foreach ($this->_nodes as $node) {
          if ($node instanceof \DOMElement) {
            $node->setAttribute($attribute, (string)$value);
          }
        }
      }
      return $this;
    }

    /**
     * Returns true if the specified attribute is present on at least one of
     * the set of matched elements.
     *
     * @param string $name
     * @return bool
     */
    public function hasAttr($name) {
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement &&
          $node->hasAttribute($name)) {
          return TRUE;
        }
      }
      return FALSE;
    }

    /**
     * Remove an attribute from each of the matched elements. If $name is NULL or *,
     * all attributes will be deleted.
     *
     * @example removeAttr.php Usage Example: FluentDOM\Query::removeAttr()
     * @param string $name
     * @throws \InvalidArgumentException
     * @return Query
     */
    public function removeAttr($name) {
      $attributes = NULL;
      if (is_string($name) && $name !== '*') {
        $attributes = array($name);
      } elseif (is_array($name)) {
        $attributes = $name;
      } elseif ($name !== '*') {
        throw new \InvalidArgumentException();
      }
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement) {
          if (is_null($attributes)) {
            for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
              /** @noinspection PhpUndefinedFieldInspection */
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
      return $this;
    }

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
     * @example toggleClass.php Usage Example: FluentDOM\Query::toggleClass()
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

    /*************************************
     * Manipulation - CSS Style Attribute
     ************************************/

    /**
     * get or set CSS values in style attributes
     *
     * @param string|array $property
     * @param NULL|string|object|callable $value
     * @throws \InvalidArgumentException
     * @return string|object|Query
     */
    public function css($property, $value = NULL) {
      if (is_string($property) && is_null($value)) {
        try {
          $firstNode = $this->getContentElement($this->_nodes);
          $properties = new Query\Css\Properties($firstNode->getAttribute('style'));
          if (isset($properties[$property])) {
            return $properties[$property];
          }
        } catch (\UnexpectedValueException $e) {
        }
        return NULL;
      } elseif (is_string($property)) {
        $propertyList = array($property => $value);
      } elseif (is_array($property) ||
        $property instanceOf \Traversable) {
        $propertyList = $property;
      } else {
        throw new \InvalidArgumentException('Invalid css property name argument type.');
      }
      //set list of properties to all elements
      foreach ($this->_nodes as $index => $node) {
        if ($node instanceof \DOMElement) {
          $properties = new Query\Css\Properties($node->getAttribute('style'));
          foreach ($propertyList as $name => $value) {
            $properties[$name] = $properties->compileValue(
              $value, $node, $index, isset($properties[$name]) ? $properties[$name] : NULL
            );
          }
          if (count($properties) > 0) {
            $node->setAttribute('style', (string)$properties);
          } elseif ($node->hasAttribute('style')) {
            $node->removeAttribute('style');
          }
        }
      }
      return $this;
    }

    /*********************************
     * Manipulation - Data Attributes
     ********************************/

    /**
     * Read a data attribute from the first node or set data attributes n all selected nodes.
     *
     * @example data.php Usage Example: FluentDOM\Query::data()
     * @param string|array $name data attribute identifier or array of data attributes to set
     * @param mixed $value
     * @return mixed
     */
    public function data($name, $value = NULL) {
      if (!is_array($name) && is_null($value)) {
        //reading
        if (isset($this->_nodes[0]) &&
          $this->_nodes[0] instanceof \DOMElement) {
          $data = new Query\Data($this->_nodes[0]);
          return $data->$name;
        }
        return NULL;
      } elseif (is_array($name)) {
        $values = $name;
      } else {
        $values = array((string)$name => $value);
      }
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement) {
          $data = new Query\Data($node);
          foreach ($values as $dataName => $dataValue) {
            $data->$dataName = $dataValue;
          }
        }
      }
      return NULL;
    }

    /**
     * Remove an data - attribute from each of the matched elements. If $name is NULL or *,
     * all data attributes will be deleted.
     *
     * @example removeData.php Usage Example: FluentDOM\Query::removeData()
     * @param string $name
     * @throws \InvalidArgumentException
     * @return Query
     */
    public function removeData($name = NULL) {
      if (is_null($name) || $name === '*') {
        $names = NULL;
      } elseif (is_string($name) && trim($name != '')) {
        $names = array($name);
      } elseif (is_array($name)) {
        $names = $name;
      } else {
        throw new \InvalidArgumentException();
      }
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement) {
          $data = new Query\Data($node);
          if (is_array($names)) {
            foreach ($names as $dataName) {
              unset($data->$dataName);
            }
          } else {
            foreach ($data as $dataName => $dataValue) {
              unset($data->$dataName);
            }
          }
        }
      }
    }

    /**
     * Validate if the element has an data attributes attached. If it is called without an
     * actual $element parameter, it will check the first matched node.
     *
     * @param \DOMElement $element
     * @return boolean
     */
    public function hasData(\DOMElement $element = NULL) {
      if (isset($element)) {
        $data = new Query\Data($element);
        return count($data) > 0;
      }
      if (isset($this->_nodes[0]) &&
        $this->_nodes[0] instanceof \DOMElement) {
        $data = new Query\Data($this->_nodes[0]);
        return count($data) > 0;
      }
      return FALSE;
    }
  }
}