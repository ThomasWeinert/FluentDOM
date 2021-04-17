<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Xpath;
  use FluentDOM\Exceptions\InvalidSource\Variable as InvalidVariableSource;
  use FluentDOM\Exceptions\NoSerializer;
  use FluentDOM\Xpath\Transformer;
  use FluentDOM\Loader\Options;
  use FluentDOM\Serializer;
  use FluentDOM\Utility\Constraints;
  use FluentDOM\Utility\Iterators\NodesIterator;

  /**
   * Implements an extended replacement for DOMNodeList.
   *
   * @property string $contentType Output type - text/xml or text/html
   * @property callable $onPrepareSelector A callback to convert the selector into xpath
   * @property-read int $length The amount of elements found by selector.
   * @property-read Document|\DOMDocument $document Internal DOMDocument object
   * @property-read XPath $xpath Internal XPath object
   */
  class Nodes implements \ArrayAccess, \Countable, \IteratorAggregate {

    public const CONTEXT_DOCUMENT = Transformer::CONTEXT_DOCUMENT;
    public const CONTEXT_SELF = Transformer::CONTEXT_SELF;
    public const CONTEXT_CHILDREN = Transformer::CONTEXT_CHILDREN;

    public const FIND_MODE_MATCH = 8;
    public const FIND_MODE_FILTER = 16;
    public const FIND_FORCE_SORT = 32;

    /**
     * @var Xpath
     */
    private $_xpath;

    /**
     * @var array
     */
    private $_namespaces = [];

    /**
     * @var \DOMDocument
     */
    private $_document;

    /**
     * Content type for output (xml, text/xml, html, text/html).
     * @var string $_contentType
     */
    private $_contentType = 'text/xml';

    /**
     * A list of loaders for different data sources
     * @var Loadable $loaders
     */
    private $_loaders;

    /**
     * A callback used to convert the selector to xpath before use
     *
     * @var callable
     */
    private $_onPrepareSelector;

    /**
     * @var Nodes|NULL
     */
    protected $_parent;

    /**
     * @var \DOMNode[]
     */
    protected $_nodes = [];

    /**
     * Use document context for expression (not selected nodes).
     * @var bool $_useDocumentContext
     */
    protected $_useDocumentContext = TRUE;

    /**
     * @var array $_loadingContext store the loaded content type and options
     */
    private $_loadingContext = [];

    /**
     * @var Serializer\Factory\Group
     */
    private $_serializerFactories;

    /**
     * @param mixed $source
     * @param NULL|string $contentType
     * @throws InvalidVariableSource
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     * @throws \LogicException
     */
    public function __construct($source = NULL, string $contentType = NULL) {
      if (NULL !== $source) {
        $this->load($source, $contentType);
      } elseif (NULL !== $contentType) {
        $this->setContentType($contentType);
      }
    }

    /**
     * Load a $source. The type of the source depends on the loaders. If no explicit loaders are set
     * it will use a set of default loaders for xml/html and json.
     *
     * @param mixed $source
     * @param string $contentType optional, default value 'text/xml'
     * @param array|\Traversable|Options $options
     * @return $this
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws InvalidVariableSource
     * @throws \OutOfBoundsException
     */
    public function load($source, string $contentType = NULL, $options = []): self {
      $contentType = $contentType ?: 'text/xml';
      $loaded = $this->prepareSource($source, $contentType, $options);
      $isResult = $loaded instanceof Loader\Result;
      if ($isResult || $loaded instanceof \DOMDocument) {
        if ($isResult) {
          /** @var Loader\Result $loaded */
          $this->_document = $loaded->getDocument();
          $this->setContentType($loaded->getContentType());
          if ($selection = $loaded->getSelection()) {
            $this->push($selection);
          }
        } else {
          /** @var \DOMDocument $loaded */
          $this->_document = $loaded;
          $this->setContentType($contentType);
        }
        $this->_xpath = NULL;
        $this->applyNamespaces();
        return $this;
      }
      throw new Exceptions\InvalidSource\Variable($source, $contentType);
    }

    /**
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return \DOMDocument|Document|Loader\Result|bool|NULL
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    private function prepareSource($source, string $contentType, $options) {
      $loaded = FALSE;
      $this->_useDocumentContext = TRUE;
      if ($source instanceof self) {
        $loaded = $source->getDocument();
      } elseif ($source instanceof \DOMDocument) {
        $loaded = $source;
      } elseif ($source instanceof \DOMNode) {
        $loaded = $source->ownerDocument;
        $this->_nodes = [$source];
        $this->_useDocumentContext = FALSE;
      } elseif ($this->loaders()->supports($contentType)) {
        $loaded = $this->loaders()->load($source, $contentType, $options);
        $this->_loadingContext = [
          'contentType' => $contentType,
          'options' => $options,
        ];
      }
      return $loaded;
    }

    /**
     * Set the loaders list.
     *
     * @param Loadable|array|\Traversable $loaders
     * @return Loadable
     * @throws \InvalidArgumentException
     */
    public function loaders($loaders = NULL): Loadable {
      if (NULL !== $loaders) {
        if ($loaders instanceof Loadable) {
          $this->_loaders = $loaders;
        } elseif (is_iterable($loaders)) {
          $this->_loaders = new Loaders($loaders);
        } else {
          throw new Exceptions\InvalidArgument(
            'loaders', [Loadable::class, 'array', \Traversable::class]
          );
        }
      } elseif (NULL === $this->_loaders) {
        $this->_loaders = \FluentDOM::getDefaultLoaders();
      }
      return $this->_loaders;
    }

    /**
     * Return the options from the original loading action, but only if the
     * content type equals the loaded content type.
     *
     * @param NULL|string $contentType
     * @return array|mixed
     */
    public function getLoadingOptions($contentType = NULL) {
      $contentType = $contentType ?: $this->_contentType;
      if (
        isset($this->_loadingContext['contentType']) &&
        $this->_loadingContext['contentType'] === $contentType
      ) {
        return $this->_loadingContext['options'];
      }
      return [];
    }

    /**
     * The item() method is used to access elements in the node list,
     * like in a DOMNodeList.
     *
     * @param int $position
     * @return \DOMElement|\DOMNode|NULL
     */
    public function item(int $position) {
      return $this->_nodes[$position] ?? NULL;
    }

    /**
     * @param string $expression
     * @param \DOMNode $contextNode
     * @return Xpath|\DOMNodeList|float|string
     * @throws \LogicException
     */
    public function xpath(string $expression = NULL, \DOMNode $contextNode = NULL) {
      if (NULL !== $expression) {
        return $this->getXpath()->evaluate($expression, $contextNode);
      }
      return $this->getXpath();
    }

    /**
     * @return Xpath
     * @throws \LogicException
     */
    private function getXpath(): Xpath {
      if ($this->_document instanceof Document) {
        return $this->_document->xpath();
      }
      if ((NULL !== $this->_xpath) && ($this->_xpath->document === $this->_document)) {
        return $this->_xpath;
      }
      $this->_xpath = new Xpath($this->getDocument());
      $this->applyNamespaces();
      return $this->_xpath;
    }

    /**
     * Register a namespace for selectors/expressions
     *
     * @param string $prefix
     * @param string $namespaceURI
     * @throws \LogicException
     */
    public function registerNamespace(string $prefix, string $namespaceURI) {
      $this->_namespaces[$prefix] = $namespaceURI;
      $document = $this->getDocument();
      if ($document instanceof Document) {
        $document->registerNamespace($prefix, $namespaceURI);
      } elseif (NULL !== $this->_xpath) {
        $this->_xpath->registerNamespace($prefix, $namespaceURI);
      }
    }

    /**
     * apply stored namespaces to attached document or xpath object
     *
     * @throws \LogicException
     */
    private function applyNamespaces() {
      $document = $this->getDocument();
      if ($document instanceof Document) {
        foreach ($this->_namespaces as $prefix => $namespaceURI) {
          $document->registerNamespace($prefix, $namespaceURI);
        }
      } elseif (NULL !== $this->_xpath) {
        foreach ($this->_namespaces as $prefix => $namespaceURI) {
          $this->_xpath->registerNamespace($prefix, $namespaceURI);
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
     * @return self
     * @throws \LogicException
     */
    public function formatOutput(string $contentType = NULL): self {
      if (NULL !== $contentType) {
        $this->setContentType($contentType);
      }
      $this->_nodes = [];
      $this->_useDocumentContext = TRUE;
      $this->_parent = NULL;
      $document = $this->getDocument();
      $document->preserveWhiteSpace = FALSE;
      $document->formatOutput = TRUE;
      if (NULL !== $document->documentElement) {
        $document->loadXML($document->saveXML());
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
     * @return static
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    protected function fetch(
      string $expression, $filter = NULL, $stopAt = NULL, int $options = 0
    ): self {
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
     */
    private function setContentType(string $value) {
      $mapping = [
        'text/xml' => 'text/xml',
        'xml' => 'text/xml',
        'application/xml' => 'text/xml',
        'html-fragment' => 'text/html',
        'text/html-fragment' => 'text/html',
        'html' => 'text/html',
        'text/html' => 'text/html',
      ];
      $normalizedValue = strtolower($value);
      $newContentType = $value;
      if (array_key_exists($normalizedValue, $mapping)) {
        $newContentType = $mapping[$normalizedValue];
      }
      if (NULL !== $this->_parent && $this->_contentType !== $newContentType) {
        $this->_parent->contentType = $newContentType;
      }
      $this->_contentType = $newContentType;
    }

    /**
     * Get the associated DOM, create one if here isn't one yet.
     *
     * @return \DOMDocument|Document
     * @throws \LogicException
     */
    public function getDocument(): \DOMDocument {
      if (NULL === $this->_document) {
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
    public function prepareSelector(string $selector, int $contextMode): string {
      if (NULL !== $this->_onPrepareSelector) {
        $onPrepareSelector = $this->_onPrepareSelector;
        return $onPrepareSelector($selector, $contextMode);
      }
      return $selector;
    }

    /**
     * Returns a callback that can be used to validate if an node
     * matches the selector.
     *
     * @param NULL|callable|string|array|\DOMNode|\Traversable $selector
     * @return callable|NULL
     * @throws \InvalidArgumentException
     */
    public function getSelectorCallback($selector) {
      if (NULL === $selector || Constraints::filterCallable($selector)) {
        /** NULL|callable $selector */
        return $selector;
      }
      if ($selector instanceof \DOMNode) {
        return static function (\DOMNode $node) use ($selector) {
          return $node->isSameNode($selector);
        };
      }
      if (\is_string($selector) && $selector !== '') {
        return function (\DOMNode $node) use ($selector) {
          return $this->matches($selector, $node);
        };
      }
      if (is_iterable($selector)) {
        return static function (\DOMNode $node) use ($selector) {
          foreach ($selector as $compareWith) {
            if (
              $compareWith instanceof \DOMNode &&
              $node->isSameNode($compareWith)
            ) {
              return TRUE;
            }
          }
          return FALSE;
        };
      }
      throw new \InvalidArgumentException('Invalid selector argument.');
    }

    /**
     * Test that selector matches context and return TRUE/FALSE
     *
     * @param string $selector
     * @param \DOMNode $context optional, default value NULL
     * @return bool
     */
    protected function matches(string $selector, \DOMNode $context = NULL): bool {
      $check = $this->xpath->evaluate(
        $this->prepareSelector($selector, self::CONTEXT_SELF), $context
      );
      if ($check instanceof \DOMNodeList) {
        return $check->length > 0;
      }
      return (bool)$check;
    }

    /**************
     * Interfaces
     *************/

    /**
     * Countable interface
     *
     * @return int
     */
    public function count(): int {
      return \count($this->_nodes);
    }

    /**
     * IteratorAggregate interface
     *
     * @return NodesIterator
     */
    public function getIterator(): NodesIterator {
      return new NodesIterator($this);
    }

    /**
     * Retrieve the matched DOM nodes in an array.
     *
     * @return \DOMNode[]
     */
    public function toArray(): array {
      return $this->_nodes;
    }

    /*
     * Interface - ArrayAccess
     */

    /**
     * Check if index exists in internal array
     *
     * @param int $offset
     * @return bool
     * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
     */
    public function offsetExists($offset): bool {
      return isset($this->_nodes[$offset]);
    }

    /**
     * Get element from internal array
     *
     * @param int $offset
     * @return \DOMElement|\DOMNode|NULL
     * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
     */
    public function offsetGet($offset) {
      return $this->_nodes[$offset] ?? NULL;
    }

    /**
     * If somebody tries to modify the internal array throw an exception.
     *
     * @param int $offset
     * @param mixed $value
     * @throws \BadMethodCallException
     * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
     */
    public function offsetSet($offset, $value) {
      throw new \BadMethodCallException('List is read only');
    }

    /**
     * If somebody tries to remove an element from the internal array throw an exception.
     *
     * @param int $offset
     * @throws \BadMethodCallException
     * @example interfaces/ArrayAccess.php Usage Example: ArrayAccess Interface
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
    public function __isset(string $name): bool {
      switch ($name) {
      case 'contentType' :
      case 'length' :
      case 'xpath' :
        return TRUE;
      case 'document' :
        return NULL !== $this->_document;
      }
      return FALSE;
    }

    /**
     * Virtual properties, read property
     *
     * @param string $name
     * @return mixed
     * @throws \LogicException
     * @throws \UnexpectedValueException
     */
    public function __get(string $name) {
      switch ($name) {
      case 'contentType' :
        return $this->_contentType;
      case 'document' :
        return $this->getDocument();
      case 'length' :
        return \count($this->_nodes);
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
     * @throws \InvalidArgumentException
     */
    public function __set(string $name, $value) {
      switch ($name) {
      case 'contentType' :
        $this->setContentType($value);
        break;
      case 'onPrepareSelector' :
        if ($callback = Constraints::filterCallable($value, TRUE, FALSE)) {
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
    public function __unset(string $name) {
      switch ($name) {
      case 'contentType' :
      case 'document' :
      case 'length' :
      case 'xpath' :
        throw new \BadMethodCallException(
          \sprintf(
            'Can not unset property %s::$%s',
            \get_class($this),
            $name
          )
        );
      }
      throw new \BadMethodCallException(
        \sprintf(
          'Can not unset non existing property %s::$%s',
          \get_class($this),
          $name
        )
      );
    }

    /**
     * Return the output of the internal dom document
     *
     * @return string
     * @throws NoSerializer
     */
    public function toString(): string {
      if ($serializer = $this->serializerFactories()->createSerializer($this->document, $this->contentType)) {
        return (string)$serializer;
      }
      throw new Exceptions\NoSerializer($this->contentType);
    }

    /**
     * Return the output of the internal dom document
     *
     * @return string
     */
    public function __toString(): string {
      try {
        /** @noinspection MagicMethodsValidityInspection */
        return $this->toString();
      } catch (\Throwable $e) {
        return '';
      }
    }

    /***************************
     * API
     ***************************/


    /**
     * Create a new instance of the same class with $this as the parent. This is used for the chaining.
     *
     * @param array|\Traversable|\DOMNode|Nodes $elements
     * @return static
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function spawn($elements = NULL): Nodes {
      $result = clone $this;
      $result->_parent = $this;
      $result->_document = $this->getDocument();
      $result->_xpath = $this->getXpath();
      $result->_nodes = [];
      $result->_loadingContext = $this->_loadingContext;
      if (NULL !== $elements) {
        $result->push($elements);
      }
      return $result;
    }

    /**
     * Return the parent FluentDOM\Nodes object.
     *
     * @return $this|self
     */
    public function end(): self {
      if ($this->_parent instanceof self) {
        return $this->_parent;
      }
      return $this;
    }

    /**
     * Push new element(s) an the internal element list
     *
     * @param \DOMNode|\Traversable|array|NULL $elements
     * @param bool $ignoreTextNodes ignore text nodes
     * @return $this
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     */
    public function push($elements, bool $ignoreTextNodes = FALSE): self {
      if (Constraints::filterNode($elements, $ignoreTextNodes)) {
        if ($elements->ownerDocument !== $this->_document) {
          throw new \OutOfBoundsException(
            'Node is not a part of this document'
          );
        }
        $this->_nodes[] = $elements;
      } elseif ($nodes = Constraints::filterNodeList($elements)) {
        $this->_useDocumentContext = FALSE;
        foreach ($nodes as $index => $node) {
          if ($node->ownerDocument !== $this->_document) {
            throw new \OutOfBoundsException(
              \sprintf('Node #%d is not a part of this document', $index)
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
     * @throws \InvalidArgumentException
     */
    public function each(callable $function, $elementsFilter = NULL): self {
      if (TRUE === $elementsFilter) {
        $filter = static function ($node) {
          return $node instanceof \DOMElement;
        };
      } else {
        $filter = Constraints::filterCallable($elementsFilter);
      }
      foreach ($this->_nodes as $index => $node) {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        if (NULL === $filter || $filter($node, $index)) {
          $function($node, $index);
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
     * @param mixed $selector selector
     * @param int $options FIND_* options CONTEXT_DOCUMENT, FIND_MODE_FILTER, FIND_FORCE_SORT
     * @return self
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/find.php Usage Example: FluentDOM::find()
     */
    public function find($selector, int $options = 0): self {
      [
        $selectorIsScalar,
        $selectorIsFilter,
        $expression,
        $contextMode,
        $fetchOptions,
      ] = $this->prepareFindContext($selector, $options);
      if ($selectorIsFilter) {
        return $this->fetch(
          $expression,
          $this->prepareSelectorAsFilter($selector, $contextMode),
          NULL,
          $fetchOptions
        );
      }
      if ($selectorIsScalar) {
        return $this->fetch(
          $this->prepareSelector($selector, $contextMode),
          NULL,
          NULL,
          $fetchOptions
        );
      }
      return $this->fetch($expression, $selector, NULL, $fetchOptions);
    }

    /**
     * @param mixed $selector
     * @param int $options
     * @return array
     */
    private function prepareFindContext($selector, int $options): array {
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
        return [$selectorIsScalar, $selectorIsFilter, $expression, $contextMode, $fetchOptions];
      }
      return [$selectorIsScalar, $selectorIsFilter, $expression, $contextMode, $fetchOptions];
    }

    /**
     * @param string $selector
     * @param int $contextMode
     * @return \Closure
     */
    private function prepareSelectorAsFilter(string $selector, int $contextMode): \Closure {
      $filter = $this->prepareSelector($selector, $contextMode);
      if (preg_match('(^(/{0,2})([a-z-]+::.*))ui', $filter, $matches)) {
        $filter = $matches[2];
      } elseif (preg_match('(^(//?)(.*))', $filter, $matches)) {
        $filter = 'self::'.$matches[2];
      }
      return function ($node) use ($filter) {
        return $this->xpath->evaluate($filter, $node)->length > 0;
      };
    }

    /**
     * Search for a given element from among the matched elements.
     *
     * @param NULL|string|\DOMNode|\Traversable $selector
     * @return int
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function index($selector = NULL): int {
      if (\count($this->_nodes) > 0) {
        if (NULL === $selector) {
          return (int)$this->xpath(
            'count(
              preceding-sibling::node()[
                self::* or (self::text() and normalize-space(.) != "")
              ]
            )',
            $this->_nodes[0]
          );
        }
        $callback = $this->getSelectorCallback($selector);
        foreach ($this->_nodes as $index => $node) {
          if ($callback($node)) {
            return $index;
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
     * @return array
     * @throws \InvalidArgumentException
     */
    public function unique(array $array): array {
      $count = \count($array);
      if ($count <= 1) {
        if ($count === 1) {
          Constraints::assertNode(
            \reset($array), 'Array must only contain dom nodes, found "%s".'
          );
        }
        return $array;
      }
      $sortable = [];
      $unsortable = [];
      foreach ($array as $node) {
        Constraints::assertNode($node, 'Array must only contain dom nodes, found "%s".');
        $hash = \spl_object_hash($node);
        if (
          ($node->parentNode instanceof \DOMNode) ||
          $node === $node->ownerDocument->documentElement) {
          /* use the document position as index, ignore duplicates */
          if (!isset($sortable[$hash])) {
            $sortable[$hash] = $node;
          }
        } elseif (!isset($unsortable[$hash])) {
          /* use the object hash as index, ignore duplicates */
          $unsortable[$hash] = $node;
        }
      }
      \uasort($sortable, new Nodes\Compare($this->xpath));
      $result = \array_values($sortable);
      \array_splice($result, \count($result), 0, \array_values($unsortable));
      return $result;
    }

    /**
     * @param Serializer\Factory\Group|NULL $factories
     * @return Serializer\Factory\Group
     */
    public function serializerFactories(Serializer\Factory\Group $factories = NULL): Serializer\Factory\Group {
      if (NULL !== $factories) {
        $this->_serializerFactories = $factories;
      } elseif (NULL === $this->_serializerFactories) {
        $this->_serializerFactories = \FluentDOM::getSerializerFactories();
      }
      return $this->_serializerFactories;
    }
  }
}
