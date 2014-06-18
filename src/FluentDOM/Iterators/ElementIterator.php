<?php
/**
 * Iterator class for FluentDOM\Element objects
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Iterators {

  use FluentDOM\Element as Element;

  /**
   * Iterator class for FluentDOM\Element objects
   *
   * @method \DOMElement getOwner()
   */
  class ElementIterator extends IndexIterator implements \RecursiveIterator {

    /**
     * Check if current iterator pointer contains a valid element
     *
     * @return boolean
     */
    public function valid() {
      $owner = $this->getOwner();
      return
        NULL !== $owner->childNodes &&
        NULL !== $owner->childNodes->item($this->_position);
    }

    /**
     * Get current iterator element
     *
     * @return \DOMNode
     */
    public function current() {
      return $this->getOwner()->childNodes->item($this->_position);
    }

    /**
     * Get child nodes of the current iterator element
     *
     * @throws \UnexpectedValueException
     * @return \RecursiveIterator
     */
    public function getChildren() {
      $element = $this->current();
      if ($element instanceof Element) {
        return new self($element);
      }
      throw new \UnexpectedValueException(
        'Called '.__METHOD__.' with invalid current element.'
      );
    }

    /**
     * Check if the current iterator element has children
     *
     * @return boolean
     */
    public function hasChildren() {
      return
        $this->valid() &&
        $this->current() instanceof Element &&
        NULL !== $this->current()->childNodes;
    }
  }
}