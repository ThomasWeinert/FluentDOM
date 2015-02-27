<?php

namespace FluentDOM\Node\ParentNode {

  use FluentDOM\Document;
  use FluentDOM\Element;
  use FluentDOM\Node\MutationMacro;
  use FluentDOM\Node\QuerySelector;

  /**
   * @property-read \DOMNode $firstChild
   * @property-read \DOMNode $lastChild
   * @property-read \DOMNode $nextSibling
   * @property-read \DOMNode $previousSibling
   */
  trait Implementation {

    abstract public function insertBefore(\DOMNode $newChild, \DOMNode $refChild = NULL);

    abstract public function appendChild(\DOMNode $newChild);

    /**
     * Returns the first element child node
     * @return Element|null
     */
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

    /**
     * Returns the last element child node
     * @return Element|null
     */
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

    /**
     * Insert nodes before the first child node
     *
     * @param mixed $nodes
     */
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

    /**
     * Append nodes as children to the node itself
     *
     * @param mixed $nodes
     */
    public function append($nodes) {
      /** @var \DOMNode|Implementation $this */
      if ($nodes = MutationMacro::expand($this, $nodes)) {
        $this->appendChild($nodes);
      }
    }
  }
}