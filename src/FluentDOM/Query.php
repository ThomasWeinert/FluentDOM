<?php
/**
 * FluentDOM\Query implements a jQuery like replacement for DOMNodeList
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {
  use FluentDOM\Nodes\Fetcher;

  /**
   * FluentDOM\Query implements a jQuery like replacement for DOMNodeList
   *
   * @property Query\Attributes $attr
   * @property Query\Data $data
   * @property Query\Css $css
   *
   * @method Query clone() Clone matched nodes and select the clones.
   * @method Query empty() Remove all child nodes from the set of matched elements.
   *
   * @method Query spawn($elements = NULL)
   * @method Query find($selector, $options = 0)
   * @method Query end()
   */
  class Query extends Nodes {

    /**
     * @var Nodes\Builder
     */
    private $_builder;

    /**
     * Virtual properties, validate existence
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
      switch ($name) {
      case 'attr' :
      case 'css' :
      case 'data' :
        return TRUE;
      }
      return parent::__isset($name);
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
        return new Query\Css($this);
      case 'data' :
        if ($node = $this->getFirstElement()) {
          return new Query\Data($node);
        } else {
          throw new \UnexpectedValueException(
            'UnexpectedValueException: first selected node is no element.'
          );
        }
      }
      return parent::__get($name);
    }

    /**
     * Block changing the readonly dynamic property
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
      switch ($name) {
      case 'attr' :
        $this->attr(
          $value instanceOf Query\Attributes ? $value->toArray() : $value
        );
        break;
      case 'css' :
        $this->css($value);
        break;
      case 'data' :
        $this->data(
          $value instanceOf Query\Data ? $value->toArray() : $value
        );
        break;
      }
      parent::__set($name, $value);
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
      case 'css' :
      case 'data' :
        throw new \BadMethodCallException(
          sprintf(
            'Can not unset property %s::$%s',
            get_class($this),
            $name
          )
        );
      }
      parent::__unset($name);
      // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * declaring an empty() or clone() method will crash the parser so we use some magic
     *
     * @param string $name
     * @param array $arguments
     * @throws \BadMethodCallException
     * @return Query
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

    /******************
     * Internal
     *****************/

    /**
     * Returns the item from the internal array if
     * if the index exists and is an DOMElement
     *
     * @param array|\Traversable
     * @return NULL|\DOMElement
     */
    private function getFirstElement() {
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
     * @param array $elements
     * @param string|array|\DOMNode|\Traversable|callable $content
     * @return Query
     */
    private function wrapNodes($elements, $content) {
      $result = array();
      $wrapperTemplate = NULL;
      $callback = Constraints::isCallable($content, FALSE, TRUE);
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
          list($target, $wrapper) = $this->build()->getWrapperNodes(
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

    /**
     * @param \DOMNode $node
     * @return Nodes\Modifier
     */
    private function modify(\DOMNode $node) {
      return new Nodes\Modifier($node);
    }

    /**
     * @return Nodes\Builder
     */
    private function build() {
      if (NULL === $this->_builder) {
        $this->_builder = new Nodes\Builder($this);
      }
      return $this->_builder;
    }

    /**
     * Use a handler callback to apply a content argument to each node $targetNodes. The content
     * argument can be an easy setter function
     *
     * @param array|\DOMNodeList $targetNodes
     * @param string|array|\DOMNode|\DOMNodeList|\Traversable|callable $content
     * @param callable $handler
     * @return array
     */
    private function apply($targetNodes, $content, callable $handler) {
      $result = array();
      $isSetterFunction = FALSE;
      if ($callback = Constraints::isCallable($content)) {
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
          $resultNodes = call_user_func($handler, $node, $contentNodes);
          if (is_array($resultNodes)) {
            $result = array_merge($result, $resultNodes);
          }
        }
      }
      return $result;
    }

    /**
     * Apply the content to the target nodes using the handler callback
     * and push them into a spawned Query object.
     *
     * @param array|\DOMNodeList $targetNodes
     * @param string|array|\DOMNode|\DOMNodeList|\Traversable|callable $content
     * @param callable $handler
     * @param bool $remove Call remove() on $this, remove the current selection from the DOM
     * @return Query
     */
    private function applyToSpawn($targetNodes, $content, callable $handler, $remove = FALSE) {
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
     * @param string|array|\DOMNode|\Traversable $selector
     * @param callable $handler
     * @param bool $remove Call remove() on $this, remove the current selection from the DOM
     * @return Query
     */
    private function applyToSelector($selector, callable $handler, $remove = FALSE) {
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
     * @example add.php Usage Examples: FluentDOM::add()
     * @param string $selector selector
     * @param array|\Traversable $context
     * @return Query
     */
    public function add($selector, $context = NULL) {
      $result = $this->spawn($this);
      if (isset($context)) {
        $result->push($this->spawn($context)->find($selector));
      } elseif (
        is_object($selector) ||
        (is_string($selector) && substr(ltrim($selector), 0, 1) === '<')
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
     * @return Query
     */
    public function addBack() {
      $result = $this->spawn();
      $result->push($this->_nodes);
      $result->push($this->_parent);
      return $result;
    }

    /**
     * Alias for addBack()
     *
     * @deprecated
     * @return Query
     */
    public function andSelf() {
      return $this->addBack();
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
      return $this->fetch(
        '*',
        $selector,
        NULL,
        Nodes\Fetcher::UNIQUE
      );
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
      $context = $context ? $this->spawn($context) : $this;
      return $context->fetch(
        'ancestor-or-self::*',
        $selector,
        $selector,
        Fetcher::REVERSE |Fetcher::INCLUDE_STOP
      );
    }

    /**
     * Get a set of elements containing all of the unique immediate
     * child nodes including elements and text nodes of each of the matched set of elements.
     *
     * @return Query
     */
    public function contents() {
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
      $callback = $this->getSelectorCallback($selector);
      $result = $this->spawn();
      foreach ($this->_nodes as $node) {
        if ($selector instanceof \DOMElement) {
          $expression = './/*';
        } else {
          $expression = './/node()';
        }
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
     * Checks the current selection against an expression and returns true,
     * if at least one element of the selection fits the given expression.
     *
     * @example is.php Usage Example: FluentDOM\Query::is()
     * @param string $selector selector
     * @return boolean
     */
    public function is($selector) {
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
    public function map(callable $function) {
      $result = array();
      foreach ($this->_nodes as $index => $node) {
        $mapped = $function($node, $index);
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
     * @example next.php Usage Example: FluentDOM\Query::next()
     * @param string $selector
     * @return Query
     */
    public function next($selector = NULL) {
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
     * @example nextAll.php Usage Example: FluentDOM\Query::nextAll()
     * @param string $selector selector
     * @return Query
     */
    public function nextAll($selector = NULL) {
      return $this->fetch(
        'following-sibling::*|following-sibling::text()[normalize-space(.) != ""]',
        $selector
      );
    }

    /**
     * Get all following siblings of each element up to but
     * not including the element matched by the selector.
     *
     * @param string $selector selector
     * @param string $filter selector
     * @return Query
     */
    public function nextUntil($selector = NULL, $filter = NULL) {
      return $this->fetch(
        'following-sibling::*|following-sibling::text()[normalize-space(.) != ""]',
        $filter,
        $selector
      );
    }

    /**
     * Get a set of elements containing the unique parents of the matched set of elements.
     *
     * @example parent.php Usage Example: FluentDOM\Query::parent()
     * @return Query
     */
    public function parent() {
      return $this->fetch(
        'parent::*',
        NULL,
        NULL,
        Fetcher::UNIQUE
      );
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
      return $this->fetch(
        'ancestor::*',
        $selector,
        NULL,
        Fetcher::REVERSE
      );
    }

    /**
     * Get the ancestors of each element in the current set of matched elements,
     * up to but not including the element matched by the selector.
     *
     * @param string $stopAt selector
     * @param string $filter selector
     * @return Query
     */
    public function parentsUntil($stopAt = NULL, $filter = NULL) {
      return $this->fetch(
        'ancestor::*',
        $filter,
        $stopAt,
        Nodes\Fetcher::REVERSE
      );
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
     * @example prevAll.php Usage Example: FluentDOM\Query::prevAll()
     * @param string $selector selector
     * @return Query
     */
    public function prevAll($selector = NULL) {
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
     * @param string $selector selector
     * @param string $filter selector
     * @return Query
     */
    public function prevUntil($selector = NULL, $filter = NULL) {
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
      return $this->applyToSpawn(
        $this->_nodes,
        $content,
        function($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesAfter($contentNodes);
        }
      );
    }

   /**
   * Append content to the inside of every matched element.
   *
   * @example append.php Usage Example: FluentDOM\Query::append()
   * @param string|array|\DOMNode|\Traversable|callable $content DOMNode or DOMNodeList or xml fragment string
   * @return Query
   */
    public function append($content) {
      if (empty($this->_nodes) &&
        $this->_useDocumentContext &&
        !isset($this->getDocument()->documentElement)) {
        if ($callback = Constraints::isCallable($content)) {
          $contentNode = $this->build()->getContentElement($callback(NULL, 0, ''));
        } else {
          $contentNode = $this->build()->getContentElement($content);
        }
        return $this->spawn($this->getDocument()->appendChild($contentNode));
      } else {
        return $this->applyToSpawn(
          $this->_nodes,
          $content,
          function($targetNode, $contentNodes) {
            return $this->modify($targetNode)->appendChildren($contentNodes);
          }
        );
      }
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
      return $this->applyToSelector(
        $selector,
        function($targetNode, $contentNodes) {
          return $this->modify($targetNode)->appendChildren($contentNodes);
        },
        TRUE
      );
    }

    /**
     * Insert content before each of the matched elements.
     *
     * @example before.php Usage Example: FluentDOM\Query::before()
     * @param string|array|\DOMNode|\Traversable|callable $content
     * @return Query
     */
    public function before($content) {
      return $this->applyToSpawn(
        $this->_nodes,
        $content,
        function($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesBefore($contentNodes);
        }
      );
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
        /** @var \DOMNode $node */
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
     * @example insertAfter.php Usage Example: FluentDOM\Query::insertAfter()
     * @param string|array|\DOMNode|\Traversable $selector
     * @return Query
     */
    public function insertAfter($selector) {
      return $this->applyToSpawn(
        $this->build()->getTargetNodes($selector),
        $this->_nodes,
        function($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesAfter($contentNodes);
        },
        TRUE
      );
    }

    /**
     * Insert all of the matched elements before another, specified, set of elements.
     *
     * @example insertBefore.php Usage Example: FluentDOM\Query::insertBefore()
     * @param string|array|\DOMNode|\Traversable $selector
     * @return Query
     */
    public function insertBefore($selector) {
      return $this->applyToSelector(
        $selector,
        function($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesBefore($contentNodes);
        },
        TRUE
      );
    }

    /**
     * Prepend content to the inside of every matched element.
     *
     * @example prepend.php Usage Example: FluentDOM\Query::prepend()
     * @param string|array|\DOMNode|\Traversable $content
     * @return Query
     */
    public function prepend($content) {
      return $this->applyToSpawn(
        $this->_nodes,
        $content,
        function($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertChildrenBefore($contentNodes);
        }
      );
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
      return $this->applyToSelector(
        $selector,
        function($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertChildrenBefore($contentNodes);
        },
        TRUE
      );
    }

    /**
     * Replaces the elements matched by the specified selector with the matched elements.
     *
     * @example replaceAll.php Usage Example: FluentDOM\Query::replaceAll()
     * @param string|array|\DOMNode|\Traversable $selector
     * @return Query
     */
    public function replaceAll($selector) {
      $result = $this->applyToSpawn(
        $targetNodes = $this->build()->getTargetNodes($selector),
        $this->_nodes,
        function($targetNode, $contentNodes) {
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
     * @example replaceWith.php Usage Example: FluentDOM\Query::replaceWith()
     * @param string|array|\DOMNode|\Traversable|callable $content
     * @return Query
     */
    public function replaceWith($content) {
      $this->apply(
        $this->_nodes,
        $content,
        function($targetNode, $contentNodes) {
          return $this->modify($targetNode)->insertNodesBefore($contentNodes);
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
        if ($node->parentNode instanceof \DOMNode) {
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
        $callback = Constraints::isCallable($text, FALSE, TRUE);
        foreach ($this->_nodes as $index => $node) {
          if ($callback) {
            $node->nodeValue = $callback($node, $index, $node->nodeValue);
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
      $result = $this->spawn($this->wrapNodes($this->_nodes, $content));
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
     *
     * @return array|bool
     */
    private function getGroupedNodes() {
      $current = NULL;
      $counter = 0;
      $groups = array();
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
      return count($groups) > 0 ? $groups : FALSE;
    }

    /**
     * Wrap grouped nodes
     *
     * @param array $groups
     * @param \DOMElement $template
     * @return array
     */
    private function wrapGroupedNodes(array $groups, \DOMElement $template) {
      $result = [];
      $simple = FALSE;
      foreach ($groups as $group) {
        if (isset($group[0])) {
          $node = $group[0];
          /**
           * @var \DOMElement $target
           * @var \DOMElement $wrapper
           */
          list($target, $wrapper) = $this->build()->getWrapperNodes(
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
     * @example wrapInner.php Usage Example: FluentDOM\Query::wrapInner()
     * @param string|array|\DOMNode|\Traversable $content
     * @return Query
     */
    public function wrapInner($content) {
      $elements = array();
      foreach ($this->_nodes as $node) {
        foreach ($node->childNodes as $childNode) {
          if (Constraints::isNode($childNode)) {
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
     * @example xml.php Usage Example: FluentDOM::xml()
     * @param string|callable|NULL $xml XML fragment
     * @return string|self
     */
    public function xml($xml = NULL) {
      return $this->content(
        $xml,
        function($node) {
          return $this->build()->getInnerXml($node);
        },
        function($node) {
          return $this->build()->getXmlFragment($node, TRUE);
        },
        function($node, $fragment) {
          $this->modify($node)->replaceChildren($fragment);
        }
      );
    }

    /**
     * Get the first matched node as XML or replace each
     * matched nodes with the provided fragment.
     *
     * @param string|callable|NULL $xml
     * @return string|self
     */
    public function outerXml($xml = NULL) {
      return $this->outerContent(
        $xml,
        function($node) {
          return $this->getDocument()->saveXML($node);
        },
        function($xml) {
          return $this->build()->getXmlFragment($xml, TRUE);
        }
      );
    }

    /**
     * Get the first matched node as HTML or replace each
     * matched nodes with the provided fragment.
     *
     * @param string|callable|NULL $html
     * @return string|self
     */
    public function outerHtml($html = NULL) {
      return $this->outerContent(
        $html,
        function($node) {
          return $this->getDocument()->saveHTML($node);
        },
        function($html) {
          return $this->build()->getHtmlFragment($html);
        }
      );
    }

    /**
     * Get html contents of the first matched element or set the
     * html contents of all selected element nodes.
     *
     * @param string|callable|NULL $html
     * @return string|self
     */
    public function html($html = NULL) {
      return $this->content(
        $html,
        function($node) {
          $result = '';
          foreach ($node->childNodes as $node) {
            $result .= $this->getDocument()->saveHTML($node);
          }
          return $result;
        },
        function($html) {
          return $this->build()->getHtmlFragment($html);
        },
        function($node, $fragment) {
          $this->modify($node)->replaceChildren($fragment);
        }
      );
    }

    /**
     * @param string|callable|NULL $content
     * @param callable $export
     * @param callable $import
     * @param callable $insert
     * @return $this|string
     */
    private function content($content, callable $export, callable $import, callable $insert) {
      if (isset($content)) {
        $callback = Constraints::isCallable($content, FALSE, TRUE);
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
      } elseif (isset($this->_nodes[0])) {
        return $export($this->_nodes[0]);
      }
      return '';
    }

    /**
     * @param string|callable|NULL $content
     * @param callable $export
     * @param callable $import
     * @return $this|string
     */
    private function outerContent($content, callable $export, callable $import) {
      return $this->content(
        $content,
        $export,
        $import,
        function($node, $fragment) {
          $this->modify($node)->replaceNode($fragment);
        }
      );
    }

    /****************************
     * Manipulation - Attributes
     ***************************/

    /**
     * @param string|array|NULL $names
     * @throws \InvalidArgumentException
     * @return array
     */
    private function getNamesList($names) {
      $attributes = NULL;
      if (is_array($names)) {
        $attributes = $names;
      } elseif (is_string($names) && $names !== '*' && $names !== '') {
        $attributes = array($names);
      } elseif (isset($names) && $names !== '*') {
        throw new \InvalidArgumentException();
      }
      return $attributes;
    }

    /**
     * @param string|array|\Traversable $name
     * @param string|float|int|NULL|callable $value
     * @return array|\Traversable
     * @throws \InvalidArgumentException
     */
    private function getSetterValues($name, $value) {
      if (is_string($name)) {
        return array((string)$name => $value);
      } elseif (is_array($name) || $name instanceOf \Traversable) {
        return $name;
      }
      throw new \InvalidArgumentException('Invalid css property name argument type.');
    }

    /**
     * Access a property on the first matched element or set the attribute(s) of all matched elements
     *
     * @example attr.php Usage Example: FluentDOM\Query::attr() Read an attribute value.
     * @param string|array $attribute attribute name or attribute list
     * @param string|callable $value function callback($index, $value) or value
     * @return string|Query attribute value or $this
     */
    public function attr($attribute, $value = NULL) {
      if (NULL === $value && !is_array(($attribute))) {
        //empty value - read attribute from first element in list
        $attribute = (new QualifiedName($attribute))->name;
        $node = $this->getFirstElement();
        if ($node && $node->hasAttribute($attribute)) {
          return $node->getAttribute($attribute);
        }
        return NULL;
      } else {
        $attributes = $this->getSetterValues($attribute, $value);
        // set attributes on each element
        foreach ($attributes as $key => $value) {
          $name = (new QualifiedName($key))->name;
          $callback = Constraints::isCallable($value);
          $this->each(
            function(\DOMElement $node, $index) use ($name, $value, $callback) {
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
     * @example removeAttr.php Usage Example: FluentDOM\Query::removeAttr()
     * @param string|array $name
     * @throws \InvalidArgumentException
     * @return Query
     */
    public function removeAttr($name) {
      $names = $this->getNamesList($name);
      $this->each(
        function(\DOMElement $node) use ($names) {
          /** @noinspection PhpParamsInspection */
          $attributes = NULL === $names
            ? array_keys(iterator_to_array($node->attributes))
            : $names;
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
     * @param string $class
     * @return boolean
     */
    public function hasClass($class) {
      foreach ($this->_nodes as $node) {
        if ($node instanceof \DOMElement && $node->hasAttribute('class')) {
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
     * Adds the specified classes if the switch is TRUE,
     * removes the specified classes if the switch is FALSE,
     * toggles the specified classes if the switch is NULL.
     *
     * @example toggleClass.php Usage Example: FluentDOM\Query::toggleClass()
     * @param string|callable $class
     * @param NULL|boolean $switch toggle if NULL, add if TRUE, remove if FALSE
     * @return Query
     */
    public function toggleClass($class, $switch = NULL) {
      $callback = Constraints::isCallable($class);
      $this->each(
        function(\DOMElement $node, $index) use ($class, $switch, $callback) {
          if ($callback) {
            $classString = $callback($node, $index, $node->getAttribute('class'));
          } else {
            $classString = $class;
          }
          if (empty($classString) && !(bool)$switch) {
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
     *
     * @param string $current
     * @param string $toggle
     * @param bool|NULL $switch
     * @return FALSE|string
     */
    private function changeClassString($current, $toggle, $switch) {
      $currentClasses = array_flip(
        preg_split('(\s+)', trim($current), 0, PREG_SPLIT_NO_EMPTY)
      );
      $toggleClasses = array_unique(
        preg_split('(\s+)', trim($toggle), 0, PREG_SPLIT_NO_EMPTY)
      );
      $modified = FALSE;
      foreach ($toggleClasses as $class) {
        if (
          isset($currentClasses[$class]) &&
          (NULL === $switch || FALSE === $switch)
        ) {
          unset($currentClasses[$class]);
          $modified = TRUE;
        } elseif (NULL === $switch || TRUE === $switch) {
          $currentClasses[$class] = TRUE;
          $modified = TRUE;
        }
      }
      return $modified
        ? implode(' ', array_keys($currentClasses))
        : FALSE;
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
     * @return string|NULL|$this
     */
    public function css($property, $value = NULL) {
      if (NULL === $value && is_string($property)) {
        $properties = new Query\Css\Properties((string)$this->attr('style'));
        if (isset($properties[$property])) {
          return $properties[$property];
        }
        return NULL;
      }
      $values = $this->getSetterValues($property, $value);
      //set list of properties to all elements
      $this->each(
        function(\DOMElement $node, $index) use ($values) {
          $properties = new Query\Css\Properties($node->getAttribute('style'));
          foreach ($values as $name => $value) {
            $properties[$name] = $properties->compileValue(
              $value, $node, $index, isset($properties[$name]) ? $properties[$name] : NULL
            );
          }
          if (count($properties) > 0) {
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
     * @example data.php Usage Example: FluentDOM\Query::data()
     * @param string|array $name data attribute identifier or array of data attributes to set
     * @param mixed $value
     * @return mixed
     */
    public function data($name, $value = NULL) {
      if (NULL === $value && !is_array($name)) {
        //reading
        if ($node = $this->getFirstElement()) {
          $data = new Query\Data($node);
          return $data->$name;
        }
        return NULL;
      }
      $values = $this->getSetterValues($name, $value);
      $this->each(
        function(\DOMElement $node) use ($values) {
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
     * @example removeData.php Usage Example: FluentDOM\Query::removeData()
     * @param string|array|NULL $name
     * @throws \InvalidArgumentException
     * @return Query
     */
    public function removeData($name = NULL) {
      $names = $this->getNamesList($name);
      $this->each(
        function ($node) use ($names) {
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
        },
        TRUE
      );
    }

    /**
     * Validate if the element has an data attributes attached. If it is called without an
     * actual $element parameter, it will check the first matched node.
     *
     * @param \DOMElement $element
     * @return boolean
     */
    public function hasData(\DOMElement $element = NULL) {
      if ($element || ($element = $this->getFirstElement())) {
        $data = new Query\Data($element);
        return count($data) > 0;
      }
      return FALSE;
    }
  }
}