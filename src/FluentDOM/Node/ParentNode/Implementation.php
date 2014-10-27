<?php

namespace FluentDOM\Node\ParentNode {

  use FluentDOM\Document;
  use FluentDOM\Element;
  use FluentDOM\Node\MutationMacro;

  trait Implementation {

    public function getFirstElementChild() {
      if ($this instanceof Document) {
        return $this->documentElement;
      }
      $node = $this->firstChild;
      while ($node->nextSibling instanceof \DOMNode) {
        if ($node->nextSibling instanceof Element) {
          return $node->nextSibling;
        }
        $node = $node->nextSibling;
      }
      return NULL;
    }

    public function getLastElementChild() {
      if ($this instanceof Document) {
        return $this->documentElement;
      }
      $node = $this->lastChild;
      while ($node->previousSibling instanceof \DOMNode) {
        if ($node->previousSibling instanceof Element) {
          return $node->previousSibling;
        }
        $node = $node->previousSibling;
      }
      return NULL;
    }

    public function prepend($nodes) {
      /** @var \DOMNode|Implementation $this */
      if (
        $this->firstChild instanceof \DOMNode
        && ($nodes = MutationMacro::expand($this->ownerDocument, $nodes))
      ) {
        $this->insertBefore($nodes, $this->firstChild);
      } else {
        $this->append($nodes);
      }
    }

    public function append($nodes) {
      /** @var \DOMNode|Implementation $this */
      if ($nodes = MutationMacro::expand($this->ownerDocument, $nodes)) {
        $this->appendChild($nodes, $this);
      }
    }
  }
}