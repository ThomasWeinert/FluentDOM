<?php

namespace FluentDOM\DOM\Node\NonDocumentTypeChildNode {

  use FluentDOM\DOM\Element;

  /**
   * @property-read \DOMNode $firstChild
   * @property-read \DOMNode $lastChild
   * @property-read \DOMNode $nextSibling
   * @property-read \DOMNode $previousSibling
   */
  trait Implementation {

    /**
     * @return \DOMNode|null
     */
    public function getNextElementSibling() {
      $node = $this->nextSibling;
      do {
        if ($node instanceof Element) {
          return $node;
        }
        $node = $node->nextSibling;
      } while ($node instanceof \DOMNode);
      return NULL;
    }

    /**
     * @return \DOMNode|null
     */
    public function getPreviousElementSibling() {
      $node = $this->previousSibling;
      do {
        if ($node instanceof Element) {
          return $node;
        }
        $node = $node->previousSibling;
      } while ($node instanceof \DOMNode);
      return NULL;
    }
  }
}