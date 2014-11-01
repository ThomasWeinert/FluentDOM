<?php

namespace FluentDOM\Node\NonDocumentTypeChildNode {

  use FluentDOM\Element;

  trait Implementation {

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