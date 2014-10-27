<?php

namespace FluentDOM\Node\NonDocumentTypeChildNode {

  use FluentDOM\Element;

  trait Implementation {

    public function getNextElementSibling() {
      $node = $this;
      while ($node->nextSibling instanceof \DOMNode) {
        if ($node->nextSibling instanceof Element) {
          return $node->nextSibling;
        }
        $node = $node->nextSibling;
      }
      return NULL;
    }

    public function getPreviousElementSibling() {
      $node = $this;
      while ($node->previousSibling instanceof \DOMNode) {
        if ($node->previousSibling instanceof Element) {
          return $node->previousSibling;
        }
        $node = $node->previousSibling;
      }
      return NULL;
    }
  }
}