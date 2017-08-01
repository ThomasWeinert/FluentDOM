<?php

namespace FluentDOM\DOM\Node\NonDocumentTypeChildNode {

  /**
   * @property-read \DOMNode $firstChild
   * @property-read \DOMNode $lastChild
   * @property-read \DOMNode $nextSibling
   * @property-read \DOMNode $previousSibling
   */
  trait Implementation {

    /**
     * @return \DOMNode|NULL
     */
    public function getNextElementSibling() {
      $node = $this->nextSibling;
      while ($node instanceof \DOMNode) {
        if ($node instanceof \DOMElement) {
          return $node;
        }
        $node = $node->nextSibling;
      }
      return NULL;
    }

    /**
     * @return \DOMNode|NULL
     */
    public function getPreviousElementSibling() {
      $node = $this->previousSibling;
      while ($node instanceof \DOMNode) {
        if ($node instanceof \DOMElement) {
          return $node;
        }
        $node = $node->previousSibling;
      }
      return NULL;
    }
  }
}