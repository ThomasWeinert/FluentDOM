<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM {

  use FluentDOM\Exceptions\InvalidFragmentLoader;
  use FluentDOM\Exceptions\LoadingError\EmptyResult;
  use FluentDOM\Nodes\Fetcher;
  use FluentDOM\Utility\Constraints;
  use FluentDOM\Utility\QualifiedName;

  /**
   * FluentDOM\Query implements a jQuery like replacement for DOMNodeList
   *
   * @property Query\Attributes $attr
   * @property Query\Data $data
   * @property Query\Css $css
   *
   * Define methods from parent to provide information for code completion
   * @method Query load($source, string $contentType = NULL, $options = [])
   * @method Query spawn($elements = NULL)
   * @method Query find($selector, int $options = 0)
   * @method Query end()
   * @method Query formatOutput(string $contentType = NULL)
   */
  class Query extends Nodes {

    private ?Nodes\Builder $_builder = NULL;

    /**
     * Virtual properties, validate existence
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool {
      return match ($name) {
        'attr', 'css', 'data' => TRUE,
        default => parent::__isset($name),
      };
    }

    /**
     * Virtual properties, read property
     *
     * @throws \UnexpectedValueException
     * @throws \LogicException
     */
    public function __get(string $name): mixed {
      switch ($name) {
      case 'attr' :
        return new Query\Attributes($this);
      case 'css' :
        return new Query\Css($this);
      case 'data' :
        if ($node = $this->getFirstElement()) {
          return new Query\Data($node);
        }
        throw new \UnexpectedValueException(
          'UnexpectedValueException: first selected node is no element.'
        );
      }
      return parent::__get($name);
    }

    /**
     * Block changing the readonly dynamic property
     *
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public function __set(string $name, mixed $value): void {
      switch ($name) {
      case 'attr' :
        $this->attr(
          $value instanceof Query\Attributes ? $value->toArray() : $value
        );
        return;
      case 'css' :
        $this->css($value);
        return;
      case 'data' :
        $this->data(
          $value instanceof Query\Data ? $value->toArray() : $value
        );
        return;
      }
      parent::__set($name, $value);
    }

    /**
     * Throws an exception if somebody tries to unset one
     * of the dynamic properties
     *
     * @throws \BadMethodCallException
     */
    public function __unset(string $name): void {
      switch ($name) {
      case 'attr' :
      case 'css' :
      case 'data' :
        throw new \BadMethodCallException(
          \sprintf(
            'Can not unset property %s::$%s',
            \get_class($this),
            $name
          )
        );
      }
      parent::__unset($name);
      // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /******************
     * Internal
     *****************/

    /**
     * Returns the item from the internal array if
     * if the index exists and is an DOMElement
     */
    private function getFirstElement(): ?\DOMElement {
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement) {
          return $node;
        }
      }
      return NULL;
    }

    /**
     * Wrap $content around a set of elements
     *
     * @throws EmptyResult
     * @throws \InvalidArgumentException
     */
    private function wrapNodes(
      array $elements,
      string|\DOMNode|iterable|callable $content
    ): array {
      $result = [];
      $wrapperTemplate = NULL;
      $callback = Constraints::filterCallable($content);
      if (!$callback) {
        $wrapperTemplate = $this->build()->getContentElement($content);
      }
      $simple = FALSE;
      foreach ($elements as $index => $node) {
        if ($callback) {
          $wrapperTemplate = NULL;
          $wrapContent = $callback($node, $index);
          if (!empty($wrapContent)) {
            $wrapperTemplate = $this->build()->getContentElement($wrapContent);
          }
        }
        if ($wrapperTemplate instanceof \DOMElement) {
          /**
           * @var \DOMElement $target
           * @var \DOMElement $wrapper
           */
          [$target, $wrapper] = $this->build()->getWrapperNodes(
            $wrapperTemplate,
            $simple
          );
          if ($node->parentNode instanceof \DOMNode) {
            $node->parentNode->insertBefore($wrapper, $node);
          }
          $target->appendChild($node);
          $result[] = $node;
        }
      }
      return $result;
    }

    /*********************
     * Core
     ********************/

    private function modify(\DOMNode $node): Nodes\Modifier {
      return new Nodes\Modifier($node);
    }

    private function build(): Nodes\Builder {
      if (NULL === $this->_builder) {
        $this->_builder = new Nodes\Builder($this);
      }
      return $this->_builder;
    }

    /**
     * Use a handler callback to apply a content argument to each node $targetNodes. The content
     * argument can be an easy setter function
     *
     * @throws EmptyResult
     * @throws \InvalidArgumentException
     */
    private function apply(
       array|\DOMNodeList $targetNodes,
       string|iterable|\DOMNode|callable $content,
       callable $handler
    ): array {
      $result = [];
      $isSetterFunction = FALSE;
      if ($callback = Constraints::filterCallable($content)) {
        $isSetterFunction = TRUE;
      } else {
        $contentNodes = $this->build()->getContentNodes($content);
      }
      foreach ($targetNodes as $index => $node) {
        if ($isSetterFunction) {
          $contentData = $callback($node, $index, $this->build()->getInnerXml($node));
          if (!empty($contentData)) {
            $contentNodes = $this->build()->getContentNodes($contentData);
          }
        }
        if (!empty($contentNodes)) {
          $resultNodes = $handler($node, $contentNodes);
          if (\is_array($resultNodes) && \count($resultNodes) > 0) {
            \array_push($result, ...$resultNodes);
          }
        }
      }
      return $result;
    }

    /**
     * Apply the content to the target nodes using the handler callback
     * and push them into a spawned Query object.
     */
    private function applyToSpawn(
      array|\DOMNodeList $targetNodes,
      string|iterable|\DOMNode|callable $content,
      callable $handler,
      bool $remove = FALSE
    ): static {
      $result = $this->spawn(
        $this->apply($targetNodes, $content, $handler)
      );
      if ($remove) {
        $this->remove();
      }
      return $result;
    }

    /**
     * Apply the handler the $handler to nodes defined by selector, using
     * the currently selected nodes as context.
     *
     * @param bool $remove Call remove() on $this, remove the current selection from the DOM
     */
    private function applyToSelector(
      string|\DOMNode|iterable $selector, callable $handler, bool $remove = FALSE
    ): static {
      return $this->applyToSpawn(
        $this->build()->getTargetNodes($selector),
        $this->_nodes,
        $handler,
        $remove
      );
    }

    /*********************
     * Traversing
     ********************/

    /**
     * Adds more elements, matched by the given expression, to the set of matched elements.
     *
     * @throws EmptyResult
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/add.php Usage Examples: FluentDOM::add()
     */
    public function add(
      string|iterable $selector, iterable|\DOMNode $context = NULL
    ): static {
      $result = $this->spawn($this);
      if (NULL !== $context) {
        $result->push($this->spawn($context)->find($selector));
      } elseif (
        is_object($selector) ||
        (is_string($selector) && str_starts_with(\ltrim($selector), '<'))
      ) {
        $result->push($this->build()->getContentNodes($selector));
      } else {
        $result->push($this->find($selector));
      }
      $result->_nodes = $result->unique($result->_nodes);
      return $result;
    }

    /**
     * Add the previous selection to the current selection.
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function addBack(): static {
      return $this->spawn()->push($this->_nodes)->push($this->_parent);
    }

    /**
     * Get a set of elements containing of the unique immediate
     * child nodes including only elements (not text nodes) of each
     * of the matched set of elements.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/children.php Usage Examples: FluentDOM\Query::children()
     */
    public function children(string $selector = NULL): static {
      return $this->fetch('*', $selector, NULL, Nodes\Fetcher::UNIQUE);
    }

    /**
     * Get a set of elements containing the closest parent element that matches the specified
     * selector, the starting element included.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/closest.php Usage Example: FluentDOM\Query::closest()
     */
    public function closest(string|callable $selector, iterable $context = NULL): static {
      $context = $context ? $this->spawn($context) : $this;
      return $context->fetch(
        'ancestor-or-self::*',
        $selector,
        $selector,
        Fetcher::REVERSE | Fetcher::INCLUDE_STOP
      );
    }

    /**
     * Get a set of elements containing all of the unique immediate
     * child nodes including elements and text nodes of each of the matched set of elements.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function contents(): static {
      return $this->fetch(
        '*|text()[normalize-space(.) != ""]',
        NULL,
        NULL,
        Fetcher::UNIQUE
      );
    }

    /**
     * Reduce the set of matched elements to a single element.
     *
     * @param int $position Element index (start with 0)
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/eq.php Usage Example: FluentDOM\Query::eq()
     */
    public function eq(int $position): static {
      $result = $this->spawn();
      if ($position < 0) {
        $position = \count($this->_nodes) + $position;
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
     * @param string|callable $selector selector or callback function
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example filter-expr.php Usage Example: FluentDOM\Query::filter() with selector
     * @example filter-fn.php Usage Example: FluentDOM\Query::filter() with Closure
     */
    public function filter(string|callable $selector): static {
      $callback = $this->getSelectorCallback($selector);
      $result = $this->spawn();
      foreach ($this->_nodes as $index => $node) {
        if ($callback($node, $index)) {
          $result->push($node);
        }
      }
      return $result;
    }

    /**
     * Get a set of elements containing only the first of the currently selected elements.
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function first(): static {
      return $this->eq(0);
    }

    /**
     * Retrieve the matched DOM elements in an array. A negative position will be counted from the end.
     *
     * @param int|NULL $position optional offset of a single element to get.
     */
    public function get(int $position = NULL): array|\DOMNode|NULL {
      if (NULL === $position) {
        return $this->_nodes;
      }
      if ($position < 0) {
        $position = \count($this->_nodes) + $position;
      }
      return $this->_nodes[$position] ?? NULL;
    }

    /**
     * Reduce the set of matched elements to those that have
     * a descendant that matches the selector or DOM element.
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function has(string|\DOMNode $selector): static {
      $callback = $this->getSelectorCallback($selector);
      $result = $this->spawn();
      $expression = './/node()';
      if ($selector instanceof \DOMElement) {
        $expression = './/*';
      }
      foreach ($this->_nodes as $node) {
        foreach ($this->xpath($expression, $node) as $has) {
          if ($callback($has)) {
            $result->push($node);
            break;
          }
        }
      }
      return $result;
    }

    /**
     * Checks the current selection against an expression and returns TRUE,
     * if at least one element of the selection fits the given expression.
     *
     * @example ../examples/Query/is.php Usage Example: FluentDOM\Query::is()
     */
    public function is(string $selector): bool {
      foreach ($this->_nodes as $node) {
        if ($this->matches($selector, $node)) {
          return TRUE;
        }
      }
      return FALSE;
    }

    /**
     * Get a set of elements containing only the last of the currently selected elements.
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function last(): static {
      return $this->eq(-1);
    }

    /**
     * Translate a set of elements in the FluentDOM\Query object into
     * another set of values in an array (which may, or may not contain elements).
     *
     * If the callback function returns an array each element of the array will be added to the
     * result array. All other variable types are put directly into the result array.
     *
     * @param callable $function
     * @return array
     * @example ../examples/Query/map.php Usage Example: FluentDOM\Query::map()
     */
    public function map(callable $function): array {
      $result = [];
      foreach ($this->_nodes as $index => $node) {
        $mapped = $function($node, $index);
        if ($mapped === NULL) {
          continue;
        }
        if (is_iterable($mapped)) {
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
     * @param string|callable $selector selector or callback function
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/not.php Usage Example: FluentDOM\Query::not()
     */
    public function not(string|callable $selector): static {
      $callback = $this->getSelectorCallback($selector);
      return $this->filter(
        function (\DOMNode $node, $index) use ($callback) {
          return !$callback($node, $index);
        }
      );
    }

    /**
     * Get a set of elements containing the unique next siblings of each of the
     * given set of elements.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/next.php Usage Example: FluentDOM\Query::next()
     */
    public function next(string $selector = NULL): static {
      return $this->fetch(
        'following-sibling::node()[
          self::* or (self::text() and normalize-space(.) != "")
        ][1]',
        $selector,
        NULL,
        Nodes\Fetcher::UNIQUE
      );
    }

    /**
     * Find all sibling elements after the current element.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/nextAll.php Usage Example: FluentDOM\Query::nextAll()
     */
    public function nextAll(string $selector = NULL): static {
      return $this->fetch(
        'following-sibling::*|following-sibling::text()[normalize-space(.) != ""]',
        $selector
      );
    }

    /**
     * Get all following siblings of each element up to but
     * not including the element matched by the selector.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function nextUntil(string $selector = NULL, string $filter = NULL): static {
      return $this->fetch(
        'following-sibling::*|following-sibling::text()[normalize-space(.) != ""]',
        $filter,
        $selector
      );
    }

    /**
     * Get a set of elements containing the unique parents of the matched set of elements.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/parent.php Usage Example: FluentDOM\Query::parent()
     */
    public function parent(): static {
      return $this->fetch(
        'parent::*', NULL, NULL, Fetcher::UNIQUE
      );
    }

    /**
     * Get the ancestors of each element in the current set of matched elements,
     * optionally filtered by a selector.
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/parents.php Usage Example: FluentDOM\Query::parents()
     */
    public function parents(string $selector = NULL): static {
      return $this->fetch('ancestor::*', $selector, NULL, Fetcher::REVERSE);
    }

    /**
     * Get the ancestors of each element in the current set of matched elements,
     * up to but not including the element matched by the selector.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function parentsUntil(string $stopAt = NULL, string $filter = NULL): static {
      return $this->fetch('ancestor::*', $filter, $stopAt, Nodes\Fetcher::REVERSE);
    }

    /**
     * Get a set of elements containing the unique previous siblings of each of the
     * matched set of elements.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/prev.php Usage Example: FluentDOM\Query::prev()
     */
    public function prev(string $selector = NULL): static {
      return $this->fetch(
        'preceding-sibling::node()[
          self::* or (self::text() and normalize-space(.) != "")
        ][1]',
        $selector,
        NULL,
        Nodes\Fetcher::UNIQUE
      );
    }

    /**
     * Find all sibling elements in front of the current element.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/prevAll.php Usage Example: FluentDOM\Query::prevAll()
     */
    public function prevAll(string $selector = NULL): static {
      return $this->fetch(
        'preceding-sibling::*|preceding-sibling::text()[normalize-space(.) != ""]',
        $selector,
        NULL,
        Nodes\Fetcher::REVERSE
      );
    }

    /**
     * Get all preceding siblings of each element up to but not including
     * the element matched by the selector.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function prevUntil(string $selector = NULL, string $filter = NULL): static {
      return $this->fetch(
        'preceding-sibling::*|preceding-sibling::text()[normalize-space(.) != ""]',
        $filter,
        $selector,
        Nodes\Fetcher::REVERSE
      );
    }


    /**
     * Reverse the order of the matched elements.
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function reverse(): static {
      return $this->spawn()->push(array_reverse($this->_nodes));
    }

    /**
     * Get a set of elements containing all of the unique siblings of each of the
     * matched set of elements.
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/siblings.php Usage Example: FluentDOM\Query::siblings()
     */
    public function siblings(string $selector = NULL): static {
      return $this->fetch(
        'preceding-sibling::*|
         preceding-sibling::text()[normalize-space(.) != ""]|
         following-sibling::*|
         following-sibling::text()[normalize-space(.) != ""]',
        $selector,
        NULL,
        Nodes\Fetcher::REVERSE
      );
    }

    /**
     * Selects a subset of the matched elements.
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/slice.php Usage Example: FluentDOM\Query::slice()
     */
    public function slice(int $start, int $end = NULL): static {
      $result = $this->spawn();
      if ($end === NULL) {
        $result->push(\array_slice($this->_nodes, $start));
      } elseif ($end < 0) {
        $result->push(\array_slice($this->_nodes, $start, $end));
      } elseif ($end > $start) {
        $result->push(\array_slice($this->_nodes, $start, $end - $start));
      } else {
        $result->push(\array_slice($this->_nodes, $end, $start - $end));
      }
      return $result;
    }

    /*********************
     * Manipulation
     ********************/

    /**
     * Insert content after each of the matched elements.
     *
     * @example ../examples/Query/after.php Usage Example: FluentDOM\Query::after()
     */
    public function after(string|\DOMNode|iterable|callable $content): static {
      return $this->applyToSpawn(
        $this->_nodes,
        $content,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesAfter($contentNodes);
        }
      );
    }

    /**
     * Append content to the inside of every matched element.
     *
     * @throws \InvalidArgumentException
     * @throws EmptyResult
     * @throws \LogicException
     * @example ../examples/Query/append.php Usage Example: FluentDOM\Query::append()
     */
    public function append(string|\DOMNode|iterable|callable $content): static {
      if (empty($this->_nodes) &&
        $this->_useDocumentContext &&
        !isset($this->getDocument()->documentElement)) {
        if ($callback = Constraints::filterCallable($content)) {
          $contentNode = $this->build()->getContentElement($callback(NULL, 0, ''));
        } else {
          $contentNode = $this->build()->getContentElement($content);
        }
        return $this->spawn($this->getDocument()->appendChild($contentNode));
      }
      return $this->applyToSpawn(
        $this->_nodes,
        $content,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->appendChildren($contentNodes);
        }
      );
    }

    /**
     * Append all of the matched elements to another, specified, set of elements.
     * Returns all of the inserted elements.
     *
     * @example ../examples/Query/appendTo.php Usage Example: FluentDOM\Query::appendTo()
     */
    public function appendTo(string|\DOMNode|iterable $selector): static {
      return $this->applyToSelector(
        $selector,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->appendChildren($contentNodes);
        },
        TRUE
      );
    }

    /**
     * Insert content before each of the matched elements.
     *
     * @example ../examples/Query/before.php Usage Example: FluentDOM\Query::before()
     */
    public function before(string|\DOMNode|iterable|callable $content): static {
      return $this->applyToSpawn(
        $this->_nodes,
        $content,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesBefore($contentNodes);
        }
      );
    }

    /**
     * Clone matched DOM Elements and select the clones.
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/clone.php Usage Example: FluentDOM\Query:clone()
     */
    public function clone(): static {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        $result->push($node->cloneNode(TRUE));
      }
      return $result;
    }

    /**
     * Remove all child nodes from the set of matched elements.
     *
     * @throws \InvalidArgumentException
     * @example ../examples/Query/empty.php Usage Example: FluentDOM\Query:empty()
     */
    public function empty(): static {
      $this->each(
        function (\DOMNode $node) {
          $node->nodeValue = '';
        }
      );
      $this->_useDocumentContext = TRUE;
      return $this;
    }

    /**
     * Insert all of the matched elements after another, specified, set of elements.
     *
     * @throws \InvalidArgumentException
     * @example ../examples/Query/insertAfter.php Usage Example: FluentDOM\Query::insertAfter()
     */
    public function insertAfter(string|\DOMNode|iterable $selector): static {
      return $this->applyToSpawn(
        $this->build()->getTargetNodes($selector),
        $this->_nodes,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesAfter($contentNodes);
        },
        TRUE
      );
    }

    /**
     * Insert all of the matched elements before another, specified, set of elements.
     *
     * @example ../examples/Query/insertBefore.php Usage Example: FluentDOM\Query::insertBefore()
     */
    public function insertBefore(string|\DOMNode|iterable $selector): static {
      return $this->applyToSelector(
        $selector,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesBefore($contentNodes);
        },
        TRUE
      );
    }

    /**
     * Prepend content to the inside of every matched element.
     *
     * @example ../examples/Query/prepend.php Usage Example: FluentDOM\Query::prepend()
     */
    public function prepend(string|\DOMNode|iterable|callable $content): static {
      return $this->applyToSpawn(
        $this->_nodes,
        $content,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertChildrenBefore($contentNodes);
        }
      );
    }

    /**
     * Prepend all of the matched elements to another, specified, set of elements.
     * Returns all of the inserted elements.
     *
     * @example ../examples/Query/prependTo.php Usage Example: FluentDOM\Query::prependTo()
     */
    public function prependTo(string|\DOMNode|iterable $selector): static {
      return $this->applyToSelector(
        $selector,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertChildrenBefore($contentNodes);
        },
        TRUE
      );
    }

    /**
     * Replaces the elements matched by the specified selector with the matched elements.
     *
     * @throws \InvalidArgumentException
     * @example ../examples/Query/replaceAll.php Usage Example: FluentDOM\Query::replaceAll()
     */
    public function replaceAll(string|\DOMNode|iterable $selector): static {
      $result = $this->applyToSpawn(
        $targetNodes = $this->build()->getTargetNodes($selector),
        $this->_nodes,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesBefore($contentNodes);
        },
        TRUE
      );
      $target = $this->spawn($targetNodes);
      $target->remove();
      return $result;
    }

    /**
     * Replaces all matched elements with the specified HTML or DOM elements.
     * This returns the element that was just replaced,
     * which has been removed from the DOM.
     *
     * @throws \InvalidArgumentException
     * @throws EmptyResult
     * @example ../examples/Query/replaceWith.php Usage Example: FluentDOM\Query::replaceWith()
     */
    public function replaceWith(string|\DOMNode|iterable|callable $content): static {
      $this->apply(
        $this->_nodes,
        $content,
        function ($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesBefore($contentNodes);
        }
      );
      $this->remove();
      return $this;
    }

    /**
     * Removes all matched elements from the DOM.
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/remove.php Usage Example: FluentDOM\Query::remove()
     */
    public function remove(string $selector = NULL): static {
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        if (
          $node->parentNode instanceof \DOMNode &&
          (NULL === $selector || $this->matches($selector, $node))
        ) {
          $result->push($node->parentNode->removeChild($node));
        }
      }
      return $result;
    }

    /**
     * Get the combined text contents of all matched elements or
     * set the text contents of all matched elements.
     *
     * @throws \InvalidArgumentException
     * @example ../examples/Query/text.php Usage Example: FluentDOM\Query::text()
     */
    public function text(string|callable $text = NULL): string|static {
      if (NULL !== $text) {
        $callback = Constraints::filterCallable($text);
        foreach ($this->_nodes as $index => $node) {
          $node->nodeValue = (string)($callback ? $callback($node, $index, $node->nodeValue) : $text);
        }
        return $this;
      }
      $result = '';
      foreach ($this->_nodes as $node) {
        $result .= $node->textContent;
      }
      return $result;
    }

    /**
     * Replace the parent nodes of the current selection with
     * their child nodes and remove the parent nodes.
     */
    public function unwrap(string $selector = NULL): static {
      $parents = $this->parent();
      if (NULL !== $selector) {
        $parents = $parents->filter($selector);
      }
      foreach ($parents as $parentNode) {
        while ($parentNode->firstChild) {
          $parentNode->parentNode->insertBefore($parentNode->firstChild, $parentNode);
        }
        $parentNode->parentNode->removeChild($parentNode);
      }
      return $this;
    }

    /**
     * Wrap each matched element with the specified content.
     *
     * If $content contains several elements the first one is used
     *
     * @example ../examples/Query/wrap.php Usage Example: FluentDOM\Query::wrap()
     */
    public function wrap(string|\DOMNode|iterable|callable $content): static {
      return $this->spawn($this->wrapNodes($this->_nodes, $content));
    }

    /**
     * Wrap al matched elements with the specified content
     *
     * If the matched elements are not siblings, wrap each group of siblings.
     *
     * @throws EmptyResult
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/wrapAll.php Usage Example: FluentDOM::wrapAll()
     */
    public function wrapAll(string|\DOMNode|iterable $content): static {
      $result = $this->spawn();
      if ($groups = $this->getGroupedNodes()) {
        $result->push(
          $this->wrapGroupedNodes(
            $groups, $this->build()->getContentElement($content)
          )
        );
      }
      return $result;
    }

    /**
     * group selected elements by previous node - ignore whitespace text nodes
     */
    private function getGroupedNodes(): array|bool {
      $current = NULL;
      $counter = 0;
      $groups = [];
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
      return \count($groups) > 0 ? $groups : FALSE;
    }

    /**
     * Wrap grouped nodes
     */
    private function wrapGroupedNodes(array $groups, \DOMElement $template): array {
      $result = [];
      $simple = FALSE;
      foreach ($groups as $group) {
        if (isset($group[0])) {
          $node = $group[0];
          /**
           * @var \DOMElement $target
           * @var \DOMElement $wrapper
           */
          [$target, $wrapper] = $this->build()->getWrapperNodes(
            $template,
            $simple
          );
          if ($node->parentNode instanceof \DOMNode) {
            $node->parentNode->insertBefore($wrapper, $node);
          }
          foreach ($group as $node) {
            $target->appendChild($node);
          }
          $result[] = $node;
        }
      }
      return $result;
    }

    /**
     * Wrap the inner child contents of each matched element
     * (including text nodes) with an XML structure.
     *
     * @example ../examples/Query/wrapInner.php Usage Example: FluentDOM\Query::wrapInner()
     */
    public function wrapInner(string|\DOMNode|iterable|callable $content): static {
      $elements = [];
      foreach ($this->_nodes as $node) {
        foreach ($node->childNodes as $childNode) {
          if (Constraints::filterNode($childNode)) {
            $elements[] = $childNode;
          }
        }
      }
      return $this->spawn($this->wrapNodes($elements, $content));
    }

    /**
     * Get xml contents of the first matched element or set the
     * xml contents of all selected element nodes.
     *
     * @throws \InvalidArgumentException
     * @throws InvalidFragmentLoader
     * @throws \UnexpectedValueException
     * @throws \LogicException
     * @example ../examples/Query/xml.php Usage Example: FluentDOM::xml()
     */
    public function xml(string|callable $xml = NULL): string|static {
      return $this->content(
        $xml,
        function ($node) {
          return $this->build()->getInnerXml($node);
        },
        function ($node) {
          return $this->build()->getFragment($node);
        },
        function ($node, $fragment) {
          $this->modify($node)->replaceChildren($fragment);
        }
      );
    }

    /**
     * Get the first matched node as XML or replace each
     * matched nodes with the provided fragment.
     *
     * @throws \LogicException
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws InvalidFragmentLoader
     */
    public function outerXml(string|callable $xml = NULL): string|static {
      return $this->outerContent(
        $xml,
        function ($node) {
          return $this->getDocument()->saveXML($node);
        },
        function ($xml) {
          return $this->build()->getFragment($xml);
        }
      );
    }

    /**
     * Get the first matched node as HTML or replace each
     * matched nodes with the provided fragment.
     *
     * @throws \LogicException
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws InvalidFragmentLoader
     */
    public function outerHtml(string|callable $html = NULL): string|static {
      return $this->outerContent(
        $html,
        function ($node) {
          return $this->getDocument()->saveHTML($node);
        },
        function ($html) {
          return $this->build()->getFragment($html, 'text/html');
        }
      );
    }

    /**
     * Get html contents of the first matched element or set the
     * html contents of all selected element nodes.
     *
     * @throws \LogicException
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws InvalidFragmentLoader
     */
    public function html(string|callable $html = NULL): string|static {
      return $this->content(
        $html,
        function (\DOMNode $parent) {
          $result = '';
          foreach ($parent->childNodes as $node) {
            $result .= $this->getDocument()->saveHTML($node);
          }
          return $result;
        },
        function ($html) {
          return $this->build()->getFragment($html, 'text/html');
        },
        function ($node, $fragment) {
          $this->modify($node)->replaceChildren($fragment);
        }
      );
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function content(
      string|callable|NULL $content,
      callable $export,
      callable $import,
      callable $insert
    ): string|self {
      if (NULL !== $content) {
        $callback = Constraints::filterCallable($content);
        if ($callback) {
          foreach ($this->_nodes as $index => $node) {
            $contentString = $callback($node, $index, $export($node));
            $insert($node, $import($contentString));
          }
        } else {
          $fragment = $import($content);
          foreach ($this->_nodes as $node) {
            $insert($node, $fragment);
          }
        }
        return $this;
      }
      if (isset($this->_nodes[0])) {
        return $export($this->_nodes[0]);
      }
      return '';
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function outerContent(
      string|callable|null $content, callable $export, callable $import
    ): string|static {
      return $this->content(
        $content,
        $export,
        $import,
        function (\DOMNode $node, $fragment) {
          $this->modify($node)->replaceNode($fragment);
        }
      );
    }

    /****************************
     * Manipulation - Attributes
     ***************************/

    /**
     * @throws \InvalidArgumentException
     */
    private function getNamesList(string|array $names = NULL): ?array {
      if (\is_array($names)) {
        return $names;
      }
      if (\is_string($names) && $names !== '*' && $names !== '') {
        return [$names];
      }
      if (NULL !== $names && $names !== '*') {
        throw new \InvalidArgumentException('Invalid names argument provided.');
      }
      return NULL;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getSetterValues(
      string|iterable $name,
      string|float|int|callable $value = NULL
    ): iterable {
      if (\is_string($name)) {
        return [$name => $value];
      }
      if (is_iterable($name)) {
        return $name;
      }
      throw new \InvalidArgumentException('Invalid css property name argument type.');
    }

    /**
     * Access a property on the first matched element or set the attribute(s) of all matched elements
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @example ../examples/Query/attr.php Usage Example: FluentDOM\Query::attr() Read an attribute value.
     */
    public function attr(
      string|array $attribute,
      string|float|int|bool|callable|NULL ...$attributeValue
    ): string|NULL|static {
      if (\is_string($attribute) && func_num_args() === 1) {
        //empty value - read attribute from first element in list
        $attribute = (new QualifiedName($attribute))->name;
        $node = $this->getFirstElement();
        if ($node && $node->hasAttribute($attribute)) {
          return $node->getAttribute($attribute);
        }
        return NULL;
      }
      $attributes = $this->getSetterValues($attribute, $attributeValue[0] ?? NULL);
      // set attributes on each element
      foreach ($attributes as $key => $value) {
        $name = (new QualifiedName($key))->name;
        $callback = Constraints::filterCallable($value);
        $this->each(
          function (\DOMElement $node, $index) use ($name, $value, $callback) {
            $node->setAttribute(
              $name,
              $callback
                ? (string)$callback($node, $index, $node->getAttribute($name))
                : (string)$value
            );
          },
          TRUE
        );
      }
      return $this;
    }

    /**
     * Returns TRUE if the specified attribute is present on at least one of
     * the set of matched elements.
     */
    public function hasAttr(string $name): bool {
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement && $node->hasAttribute($name)) {
          return TRUE;
        }
      }
      return FALSE;
    }

    /**
     * Remove an attribute from each of the matched elements. If $name is NULL or *,
     * all attributes will be deleted.
     *
     * @throws \InvalidArgumentException
     * @example ../examples/Query/removeAttr.php Usage Example: FluentDOM\Query::removeAttr()
     */
    public function removeAttr(string|array $name): static {
      $names = $this->getNamesList($name);
      $this->each(
        function (\DOMElement $node) use ($names) {
          $attributes = $names ?? \array_keys(\iterator_to_array($node->attributes));
          foreach ($attributes as $attribute) {
            if ($node->hasAttribute($attribute)) {
              $node->removeAttribute($attribute);
            }
          }
        },
        TRUE
      );
      return $this;
    }

    /*************************
     * Manipulation - Classes
     ************************/

    /**
     * Adds the specified class(es) to each of the set of matched elements.
     */
    public function addClass(string|callable $class): static {
      return $this->toggleClass($class, TRUE);
    }

    /**
     * Returns TRUE if the specified class is present on at least one of the set of matched elements.
     */
    public function hasClass(string $class): bool {
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement && $node->hasAttribute('class')) {
          $classes = \preg_split('(\s+)', trim($node->getAttribute('class')));
          if (\is_array($classes) && \in_array($class, $classes, TRUE)) {
            return TRUE;
          }
        }
      }
      return FALSE;
    }

    /**
     * Removes all or the specified class(es) from the set of matched elements.
     *
     * @throws \InvalidArgumentException
     */
    public function removeClass(string|callable $class = ''): static {
      return $this->toggleClass($class, FALSE);
    }

    /**
     * Adds the specified classes if the switch is TRUE,
     * removes the specified classes if the switch is FALSE,
     * toggles the specified classes if the switch is NULL.
     *
     * @param NULL|bool $switch toggle if NULL, add if TRUE, remove if FALSE
     *
     * @throws \InvalidArgumentException
     * @example ../examples/Query/toggleClass.php Usage Example: FluentDOM\Query::toggleClass()
     */
    public function toggleClass(string|callable $class, bool $switch = NULL): static {
      $callback = Constraints::filterCallable($class);
      $this->each(
        function (\DOMElement $node, $index) use ($class, $switch, $callback) {
          $classString = $callback ? $callback($node, $index, $node->getAttribute('class')) : $class;
          if (empty($classString) && !$switch) {
            if ($node->hasAttribute('class')) {
              $node->removeAttribute('class');
            }
          } else {
            $modified = $this->changeClassString(
              $node->getAttribute('class'),
              $classString,
              $switch
            );
            if (FALSE !== $modified) {
              if (empty($modified)) {
                $node->removeAttribute('class');
              } else {
                $node->setAttribute('class', $modified);
              }
            }
          }
        },
        TRUE
      );
      return $this;
    }

    /**
     * Change a class string
     *
     * Adds the specified classes if the switch is TRUE,
     * removes the specified classes if the switch is FALSE,
     * toggles the specified classes if the switch is NULL.
     */
    private function changeClassString(
      string $current, string $toggle, bool $switch = NULL
    ): ?string {
      $currentClasses = \array_flip(
        \preg_split('(\s+)', \trim($current), 0, PREG_SPLIT_NO_EMPTY) ?: []
      );
      $toggleClasses = \array_unique(
        \preg_split('(\s+)', \trim($toggle), 0, PREG_SPLIT_NO_EMPTY) ?: []
      );
      $modified = FALSE;
      foreach ($toggleClasses as $class) {
        if (
          (NULL === $switch || FALSE === $switch) &&
          isset($currentClasses[$class])
        ) {
          unset($currentClasses[$class]);
          $modified = TRUE;
        } elseif (NULL === $switch || TRUE === $switch) {
          $currentClasses[$class] = TRUE;
          $modified = TRUE;
        }
      }
      return $modified
        ? \implode(' ', \array_keys($currentClasses))
        : NULL;
    }

    /*************************************
     * Manipulation - CSS Style Attribute
     ************************************/

    /**
     * get or set CSS values in style attributes
     *
     * @throws \InvalidArgumentException
     */
    public function css(
      string|array|Query\Css\Properties $property = NULL,
      callable|float|int|string|NULL ...$arguments
    ): NULL|string|static {
      if (\is_string($property) && \count($arguments) === 0) {
        $properties = new Query\Css\Properties((string)$this->attr('style'));
        return $properties[$property] ?? NULL;
      }
      $values = $this->getSetterValues($property, $arguments[0] ?? NULL);
      //set list of properties to all elements
      $this->each(
        function (\DOMElement $node, $index) use ($values) {
          $properties = new Query\Css\Properties($node->getAttribute('style'));
          foreach ($values as $name => $value) {
            $properties[(string)$name] = $properties->compileValue(
              $value, $node, $index, $properties[(string)$name] ?? NULL
            );
          }
          if (\count($properties) > 0) {
            $node->setAttribute('style', (string)$properties);
          } elseif ($node->hasAttribute('style')) {
            $node->removeAttribute('style');
          }
        },
        TRUE
      );
      return $this;
    }

    /*********************************
     * Manipulation - Data Attributes
     ********************************/

    /**
     * Read a data attribute from the first node or set data attributes on all selected nodes.
     *
     * @throws \InvalidArgumentException
     * @example ../examples/Query/data.php Usage Example: FluentDOM\Query::data()
     */
    public function data(
      string|array $name, mixed ...$arguments
    ): static|array|string|NULL {
      if (\is_string($name) && \count($arguments) === 0) {
        if ($node = $this->getFirstElement()) {
          return (new Query\Data($node))->$name;
        }
        return NULL;
      }
      $values = $this->getSetterValues($name, $arguments[0] ?? NULL);
      $this->each(
        function (\DOMElement $node) use ($values) {
          $data = new Query\Data($node);
          foreach ($values as $dataName => $dataValue) {
            $data->$dataName = $dataValue;
          }
        },
        TRUE
      );
      return $this;
    }

    /**
     * Remove an data - attribute from each of the matched elements. If $name is NULL or *,
     * all data attributes will be deleted.
     *
     * @throws \InvalidArgumentException
     * @example ../examples/Query/removeData.php Usage Example: FluentDOM\Query::removeData()
     */
    public function removeData(string|array $name = NULL): static {
      $names = $this->getNamesList($name);
      $this->each(
        function ($node) use ($names) {
          $data = new Query\Data($node);
          if (\is_array($names)) {
            foreach ($names as $dataName) {
              unset($data->$dataName);
            }
          } else {
            foreach ($data as $dataName => $dataValue) {
              unset($data->$dataName);
            }
          }
        },
        TRUE
      );
      return $this;
    }

    /**
     * Validate if the element has an data attributes attached. If it is called without an
     * actual $element parameter, it will check the first matched node.
     */
    public function hasData(\DOMElement $element = NULL): bool {
      if ($element || ($element = $this->getFirstElement())) {
        $data = new Query\Data($element);
        return \count($data) > 0;
      }
      return FALSE;
    }
  }
}
