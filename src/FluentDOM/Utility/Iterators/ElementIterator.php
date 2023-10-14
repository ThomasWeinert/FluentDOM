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

namespace FluentDOM\Utility\Iterators {

  use FluentDOM\DOM\Element;

  /**
   * Iterator class for FluentDOM\DOM\Element objects
   */
  class ElementIterator extends IndexIterator implements \RecursiveIterator {

    public function __construct(
      private Element $element
    ) {
      parent::__construct($element);
    }

    /**
     * Check if current iterator pointer contains a valid element
     */
    public function valid(): bool {
      return
        NULL !== $this->element->childNodes->item($this->_position);
    }

    /**
     * Get current iterator element
     */
    public function current(): ?\DOMNode {
      return $this->element->childNodes->item($this->_position);
    }

    /**
     * Get child nodes of the current iterator element
     *
     * @throws \UnexpectedValueException
     */
    public function getChildren(): self {
      $node = $this->current();
      if ($node instanceof Element) {
        return new self($node);
      }
      throw new \UnexpectedValueException(
        'Called '.__METHOD__.' with invalid current element.'
      );
    }

    /**
     * Check if the current iterator element has children
     */
    public function hasChildren(): bool {
      return
        $this->valid() &&
        $this->current() instanceof Element &&
        NULL !== $this->current()->childNodes;
    }
  }
}
