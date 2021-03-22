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

namespace FluentDOM\Query {

  use FluentDOM\Query;

  /**
   * FluentDOMCss is used for the FluentDOM:css property, providing an array like interface
   * to the css properties in the style attribute of the selected nodes(s)
   *
   * It acts like the FluentDOMStyle::css() method. If you read css properties it uses the first
   * selected node. Write actions are applied to all matches element nodes.
   *
   * @package FluentDOM
   */
  class Css implements \ArrayAccess, \Countable, \IteratorAggregate {

    /**
     * owner object
     * @var Query
     */
    private $_query;


    /**
     * Store the FluentDOM instance for later use and decode the style string into an array
     *
     * @param Query $query
     */
    public function __construct(Query $query) {
      $this->_query = $query;
    }

    public function getOwner(): Query {
      return $this->_query;
    }

    /**
     * Get the style properties from the first node in the Query object
     *
     * @return Css\Properties|NULL
     */
    private function getStyleProperties(): ?Css\Properties {
      if (isset($this->_query[0])) {
        $node = $this->_query[0];
        if ($node instanceof \DOMElement) {
          return new Css\Properties($node->getAttribute('style'));
        }
      }
      return NULL;
    }

    /**
     * Allow to use isset() and array syntax to check if a css property is set on
     * the first matched node.
     *
     * @see \ArrayAccess::offsetExists()
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool {
      if ($properties = $this->getStyleProperties()) {
        return isset($properties[$offset]);
      }
      return FALSE;
    }

    /**
     * Allow to use array syntax to read a css property value from first matched node.
     *
     * @see ArrayAccess::offsetGet()
     * @param mixed $offset
     * @return string|NULL
     */
    public function offsetGet($offset): ?string {
      if ($properties = $this->getStyleProperties()) {
        return $properties[$offset];
      }
      return NULL;
    }

    /**
     * Allow to use array syntax to change a css property value on all matched nodes.
     *
     * @see ArrayAccess::offsetSet()
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void {
      $this->_query->css($offset, $value);
    }

    /**
     * Allow to use unset and array syntax to remove a css property value on
     * all matched nodes.
     *
     * @see ArrayAccess::offsetUnset()
     * @param mixed $offset
     */
    public function offsetUnset($offset): void {
      foreach ($this->_query as $node) {
        if ($node instanceof \DOMElement &&
          $node->hasAttribute('style')) {
          $properties = new Css\Properties($node->getAttribute('style'));
          unset($properties[$offset]);
          if (\count($properties) > 0) {
            $node->setAttribute('style', (string)$properties);
          } else {
            $node->removeAttribute('style');
          }
        }
      }
    }

    /**
     * Get an iterator for the properties
     *
     * @see IteratorAggregate::getIterator()
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      if ($properties = $this->getStyleProperties()) {
        return $properties->getIterator();
      }
      return new \EmptyIterator();
    }

    /**
     * Get the property count of the first selected node
     *
     * @see Countable::count()
     * @return int
     */
    public function count(): int {
      if ($properties = $this->getStyleProperties()) {
        return \count($properties);
      }
      return 0;
    }
  }
}
