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
      do {
        if ($node instanceof Element) {
          return $node;
        }
        $node = $node->nextSibling;
      } while ($node instanceof \DOMNode);
      return NULL;
    }

    public function getLastElementChild() {
      if ($this instanceof Document) {
        return $this->documentElement;
      }
      /** @var \DOMNode $this */
      $node = $this->lastChild;
      do {
        if ($node instanceof Element) {
          return $node;
        }
        $node = $node->previousSibling;
      } while ($node instanceof \DOMNode);
      return NULL;
    }

    public function prepend($nodes) {
      /** @var \DOMNode|Implementation $this */
      if (
        $this->firstChild instanceof \DOMNode
        && ($nodes = MutationMacro::expand($this, $nodes))
      ) {
        $this->insertBefore($nodes, $this->firstChild);
      } else {
        $this->append($nodes);
      }
    }

    public function append($nodes) {
      /** @var \DOMNode|Implementation $this */
      if ($nodes = MutationMacro::expand($this, $nodes)) {
        $this->appendChild($nodes);
      }
    }
  }
}