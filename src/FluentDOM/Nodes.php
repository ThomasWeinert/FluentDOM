<?php
/**
 * Implements an extended replacement for DOMNodeList.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {
  use FluentDOM\Xpath\Transformer;

  /**
   * Implements an extended replacement for DOMNodeList.
   *
   * @property string $contentType Output type - text/xml or text/html
   * @property callable $onPrepareSelector A callback to convert the selector into xpath
   * @property-read integer $length The amount of elements found by selector.
   * @property-read Document|\DOMDocument $document Internal DOMDocument object
   * @property-read XPath $xpath Internal XPath object
   */
  class Nodes implements \ArrayAccess, \Countable, \IteratorAggregate {

    const CONTEXT_DOCUMENT = Transformer::CONTEXT_DOCUMENT;
    const CONTEXT_SELF = Transformer::CONTEXT_SELF;
    const CONTEXT_CHILDREN = Transformer::CONTEXT_CHILDREN;

    const FIND_MODE_MATCH = 8;
    const FIND_MODE_FILTER = 16;
    const FIND_FORCE_SORT = 32;

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
     * @var Nodes|NULL
     */
    protected $_parent = NULL;

    /**
     * @var \DOMNode[]
     */
    protected $_nodes = array();

    /**
     * Use document context for expression (not selected nodes).
     * @var boolean $_useDocumentContext
     */
    protected $_useDocumentContext = TRUE;

    /**
     * @param mixed $source
     * @param null|string $contentType
     */
    public function __construct($source = NULL, $contentType = 'text/xml') {
      if (isset($source)) {
        $this->load($source, $contentType);
      } elseif (isset($contentType)) {
        $this->setContentType($contentType);
      }
    }

    /**
     * Load a $source. The type of the source depends on the loaders. If no explicit loaders are set
     * it will use a set of default loaders for xml/html and json.
     *
     * @param mixed $source
     * @param string $contentType optional, default value 'text/xml'
     * @param array $options
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function load($source, $contentType = 'text/xml', array $options = []) {
      $dom = FALSE;
      $this->_useDocumentContext = TRUE;
      if ($source instanceof Nodes) {
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
        $this->_xpath = NULL;
        $this->applyNamespaces();
        return $this;
      }
      throw new Exceptions\InvalidSource($source, $contentType);
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
          throw new Exceptions\InvalidArgument(
            'loaders', ['FluentDOM\Loadable', 'array', '\Traversable']
          );
        }
      } elseif (NULL === $this->_loaders) {
        $this->_loaders = \FluentDOM::getDefaultLoaders();
      }
      return $this->_loaders;
    }

    /**
     * The item() method is used to access elements in the node list,
     * like in a DOMNodeList.
     *
     * @param integer $position
     * @return \DOMElement|\DOMNode
     */
    public function item($position) {
      return isset($this->_nodes[$position]) ? $this->_nodes[$position] : NULL;
    }

    /**
     * @param string $expression
     * @param \DOMNode $contextNode
     * @return Xpath|\DOMNodeList|float|string
     */
    public function xpath($expression = NULL, $contextNode = NULL) {
      if (isset($expression)) {
        return $this->getXpath()->evaluate($expression, $contextNode);
      } else {
        return $this->getXpath();
      }
    }

    /**
     * @return Xpath
     */
    private function getXpath() {
      if ($this->_document instanceof Document) {
        return $this->_document->xpath();
      } elseif (
        isset($this->_xpath) &&
        (\FluentDOM::$isHHVM || $this->_xpath->document === $this->_document)
      ) {
        return $this->_xpath;
      } else {
        $this->_xpath = new Xpath($this->getDocument());
        $this->applyNamespaces();
        return $this->_xpath;
      }
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
     * Formats the current document, resets internal node array and other properties.
     *
     * The document is saved and reloaded, all variables with DOMNodes
     * of this document will get invalid.
     *
     * @param string $contentType
     * @return Nodes
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
     * Fetch spawns and fills a Nodes instance.
     *
     * @param string $expression Xpath expression
     * @param NULL|string|callable|\DOMNode|array|\Traversable $filter
     * @param NULL|string|callable|\DOMNode|array|\Traversable $stopAt
     * @param int $options
     * @return Nodes
     */
    protected function fetch(
      $expression, $filter = NULL, $stopAt = NULL, $options = 0
    ) {
      return $this->spawn(
        (new Nodes\Fetcher($this))->fetch(
          $expression,
          $this->getSelectorCallback($filter),
          $this->getSelectorCallback($stopAt),
          $options
        )
      );
    }

    /**
     * Setter for Nodes::_contentType property
     *
     * @param string $value
     * @param bool $silentFallback
     * @throws \Exception
     * @throws \UnexpectedValueException
     */
    private function setContentType($value, $silentFallback = FALSE) {
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
        if ($silentFallback) {
          $newContentType = 'text/xml';
        } else {
          throw new \UnexpectedValueException('Invalid content type value');
        }
      }
      if (isset($this->_parent) && $this->_contentType != $newContentType) {
        $this->_parent->contentType = $newContentType;
      }
      $this->_contentType = $newContentType;
    }

    /**
     * Get the associated DOM, create one if here isn't one yet.
     *
     * @return \DOMDocument|Document
     */
    public function getDocument() {
      if (!($this->_document instanceof \DOMDocument)) {
        $this->_document = new Document();
        $this->applyNamespaces();
      }
      return $this->_document;
    }
    /**
     * Use callback to convert selector if it is set.
     *
     * @param string $selector
     * @param int $contextMode
     * @return string
     */
    public function prepareSelector($selector, $contextMode) {
      if (isset($this->_onPrepareSelector)) {
        return call_user_func($this->_onPrepareSelector, $selector, $contextMode);
      }
      return $selector;
    }

    /**
     * Returns a callback that can be used to validate if an node
     * matches the selector.
     *
     * @throws \InvalidArgumentException
     * @param NULL|callable|string|array|\DOMNode|\Traversable $selector
     * @return callable|null
     */
    public function getSelectorCallback($selector) {
      if (NULL === $selector || Constraints::isCallable($selector)) {
        return $selector;
      } elseif ($selector instanceof \DOMNode) {
        return function(\DOMNode $node) use ($selector) {
          return $node->isSameNode($selector);
        };
      } elseif (is_string($selector) && $selector !== '') {
        return function(\DOMNode $node) use ($selector) {
          return $this->matches($selector, $node);
        };
      } elseif (
        $selector instanceof \Traversable || is_array($selector)
      ) {
        return function(\DOMNode $node) use ($selector) {
          foreach ($selector as $compareWith) {
            if (
              $compareWith instanceof \DOMNode &&
              $node->isSameNode($compareWith)
            ){
              return TRUE;
            }
          }
          return FALSE;
        };
      }
      throw new \InvalidArgumentException('Invalid selector argument.');
    }

    /**
     * Test that selector matches context and return true/false
     *
     * @param string $selector
     * @param \DOMNode $context optional, default value NULL
     * @return boolean
     */
    protected function matches($selector, \DOMNode $context = NULL) {
      $check = $this->xpath->evaluate(
        $this->prepareSelector($selector, self::CONTEXT_SELF), $context
      );
      if ($check instanceof \DOMNodeList) {
        return $check->length > 0;
      } else {
        return (bool)$check;
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
     * @return Iterators\NodesIterator
     */
    public function getIterator() {
      return new Iterators\NodesIterator($this);
    }

    /**
     * Retrieve the matched DOM nodes in an array.
     *
     * @return \DOMNode[]
     */
    public function toArray() {
      return $this->_nodes;
    }

    /*
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
      case 'contentType' :
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
      case 'contentType' :
        return $this->_contentType;
      case 'document' :
        return $this->getDocument();
      case 'length' :
        return count($this->_nodes);
      case 'xpath' :
        return $this->getXpath();
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
      case 'contentType' :
        $this->setContentType($value);
        break;
      case 'onPrepareSelector' :
        if ($callback = Constraints::isCallable($value, TRUE, FALSE)) {
          $this->_onPrepareSelector = $callback;
        }
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
     * of the dynamic properties
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
     * Return the XML output of the internal dom document
     *
     * @return string
     */
    public function __toString() {
      switch ($this->contentType) {
      case 'html' :
      case 'text/html' :
        return $this->document->saveHTML();
      default :
        return $this->document->saveXML();
      }
    }

    /***************************
     * API
     ***************************/


    /**
     * Create a new instance of the same class with $this as the parent. This is used for the chaining.
     *
     * @param array|\Traversable|\DOMNode|Nodes $elements
     * @return Nodes
     */
    public function spawn($elements = NULL) {
      $result = clone $this;
      $result->_parent = $this;
      $result->_document = $this->getDocument();
      $result->_xpath = $this->getXpath();
      $result->_nodes = array();
      if (isset($elements)) {
        $result->push($elements);
      }
      return $result;
    }

    /**
     * Return the parent FluentDOM\Nodes object.
     *
     * @return Nodes
     */
    public function end() {
      if ($this->_parent instanceof Nodes) {
        return $this->_parent;
      } else {
        return $this;
      }
    }

    /**
     * Push new element(s) an the internal element list
     *
     * @param \DOMNode|\Traversable|array|NULL $elements
     * @param boolean $ignoreTextNodes ignore text nodes
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function push($elements, $ignoreTextNodes = FALSE) {
      if (Constraints::isNode($elements, $ignoreTextNodes)) {
        if ($elements->ownerDocument !== $this->_document) {
          throw new \OutOfBoundsException(
            'Node is not a part of this document'
          );
        }
        $this->_nodes[] = $elements;
      } elseif ($nodes = Constraints::isNodeList($elements)) {
        $this->_useDocumentContext = FALSE;
        foreach ($nodes as $index => $node) {
          if ($node->ownerDocument !== $this->_document) {
            throw new \OutOfBoundsException(
              sprintf('Node #%d is not a part of this document', $index)
            );
          }
          $this->_nodes[] = $node;
        }
      } elseif (NULL !== $elements) {
        throw new \InvalidArgumentException('Invalid elements variable.');
      }
      return $this;
    }

    /**
     * Execute a function within the context of every matched element.
     *
     * If $elementsOnly is a callable the return value defines if
     * it is called for that node.
     *
     * If $elementsOnly is set to TRUE, only element nodes are used.
     *
     * @param callable $function
     * @param callable|bool|NULL $elementsFilter
     * @return $this
     */
    public function each(callable $function, $elementsFilter = NULL) {
      if (TRUE === $elementsFilter) {
        $filter = function($node) {
          return $node instanceof \DOMElement;
        };
      } else {
        $filter = Constraints::isCallable($elementsFilter);
      }
      foreach ($this->_nodes as $index => $node) {
        if (NULL === $filter || $filter($node, $index)) {
          call_user_func($function, $node, $index);
        }
      }
      return $this;
    }

    /**
     * Searches for descendant elements that match the specified expression.
     *
     * If the $selector is an node or a list of nodes all descendants that
     * match that node/node list are returned.
     *
     * self::CONTEXT_DOCUMENT will use the document element as context. Otherwise the currently
     * nodes are used as contexts
     *
     * self::FIND_MODE_FILTER will use the $selector only as filter and not execute it directly.
     * this is the like the jQuery specification - but a lot slower and needs more memory.
     * Additionally in this mode, it will find only element nodes.     *
     *
     * @example find.php Usage Example: FluentDOM::find()
     * @param mixed $selector selector
     * @param integer $options FIND_* options CONTEXT_DOCUMENT, FIND_MODE_FILTER, FIND_FORCE_SORT
     * @return Nodes
     */
    public function find($selector, $options = 0) {
      list(
        $selectorIsScalar,
        $selectorIsFilter,
        $expression,
        $contextMode,
        $fetchOptions
      ) = $this->prepareFindContext($selector, $options);
      if ($selectorIsFilter) {
        return $this->fetch(
          $expression,
          $this->prepareSelectorAsFilter($selector, $contextMode),
          NULL,
          $fetchOptions
        );
      } elseif ($selectorIsScalar) {
        return $this->fetch(
          $this->prepareSelector($selector, $contextMode),
          NULL,
          NULL,
          $fetchOptions
        );
      } else {
        return $this->fetch($expression, $selector, NULL, $fetchOptions);
      }
    }

    /**
     * @param mixed $selector
     * @param int $options
     * @return array
     */
    private function prepareFindContext($selector, $options) {
      $useDocumentContext = $this->_useDocumentContext ||
        ($options & self::CONTEXT_DOCUMENT) === self::CONTEXT_DOCUMENT;
      $selectorIsScalar = is_scalar($selector) || NULL === $selector;
      $selectorIsFilter = $selectorIsScalar &&
        ($options & self::FIND_MODE_FILTER) === self::FIND_MODE_FILTER;
      if ($useDocumentContext) {
        $expression = $selectorIsFilter ? '//*' : '//*|//text()';
        $contextMode = self::CONTEXT_DOCUMENT;
        $fetchOptions = Nodes\Fetcher::IGNORE_CONTEXT;
      } else {
        $expression = $selectorIsFilter ? './/*' : './/*|.//text()';
        $contextMode = self::CONTEXT_CHILDREN;
        $fetchOptions = Nodes\Fetcher::UNIQUE;
      }
      if (($options & self::FIND_FORCE_SORT) === self::FIND_FORCE_SORT) {
        $fetchOptions |= Nodes\Fetcher::FORCE_SORT;
        return array($selectorIsScalar, $selectorIsFilter, $expression, $contextMode, $fetchOptions);
      }
      return array($selectorIsScalar, $selectorIsFilter, $expression, $contextMode, $fetchOptions);
    }

    private function prepareSelectorAsFilter($selector, $contextMode) {
      $filter = $this->prepareSelector($selector, $contextMode);
      if (preg_match('(^(/{0,2})([a-z-]+::.*))ui', $filter, $matches)) {
        $filter = $matches[2];
      } elseif (preg_match('(^(//?)(.*))', $filter, $matches)) {
        $filter = 'self::'.$matches[2];
      }
      return function($node) use ($filter) {
        return $this->xpath->evaluate($filter, $node)->length > 0;
      };
    }

    /**
     * Search for a given element from among the matched elements.
     *
     * @param NULL|string|\DOMNode|\Traversable $selector
     * @return integer
     */
    public function index($selector = NULL) {
      if (count($this->_nodes) > 0) {
        if (NULL === $selector) {
          return $this->xpath(
            'count(
              preceding-sibling::node()[
                self::* or (self::text() and normalize-space(.) != "")
              ]
            )',
            $this->_nodes[0]
          );
        } else {
          $callback = $this->getSelectorCallback($selector);
          foreach ($this->_nodes as $index => $node) {
            if ($callback($node)) {
              return $index;
            }
          }
        }
      }
      return -1;
    }

    /**
     * Sorts an array of DOM nodes based on document position, in place, with the duplicates removed.
     * Note that this only works on arrays of DOM nodes, not strings or numbers.
     *
     * @param \DOMNode[] $array array of DOM nodes
     * @throws \InvalidArgumentException
     * @return array
     */
    public function unique(array $array) {
      $count = count($array);
      if ($count <= 1) {
        if ($count == 1) {
          Constraints::assertNode(
            reset($array), 'Array must only contain dom nodes, found "%s".'
          );
        }
        return $array;
      }
      $sortable = array();
      $unsortable = array();
      foreach ($array as $node) {
        Constraints::assertNode($node, 'Array must only contain dom nodes, found "%s".');
        $hash = spl_object_hash($node);
        if (
          ($node->parentNode instanceof \DOMNode) ||
          $node === $node->ownerDocument->documentElement) {
          /* use the document position as index, ignore duplicates */
          if (!isset($sortable[$hash])) {
            $sortable[$hash] = $node;
          }
        } else {
          /* use the object hash as index, ignore duplicates */
          if (!isset($unsortable[$hash])) {
            $unsortable[$hash] = $node;
          }
        }
      }
      uasort($sortable, new Nodes\Compare($this->xpath));
      $result = array_values($sortable);
      array_splice($result, count($result), 0, array_values($unsortable));
      return $result;
    }
  }
}