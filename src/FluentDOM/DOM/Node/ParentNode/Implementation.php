<?php

namespace FluentDOM\DOM\Node\ParentNode {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\DOM\Node\MutationMacro;

  /**
   * @property-read \DOMNode $firstChild
   * @property-read \DOMNode $lastChild
   * @property-read \DOMNode $nextSibling
   * @property-read \DOMNode $previousSibling
   */
  trait Implementation {

    /**
     * @param \DOMNode $newChild
     * @param \DOMNode|NULL $refChild
     * @return \DOMNode
     */
    abstract public function insertBefore(\DOMNode $newChild,\DOMNode $refChild = NULL);

    /**
     * @param \DOMNode $newChild
     * @return \DOMNode
     */
    abstract public function appendChild(\DOMNode $newChild);

    /**
     * Returns the first element child node
     * @return Element|NULL
     */
    public function getFirstElementChild() {
      if ($this instanceof Document) {
        return $this->documentElement;
      }
      $node = $this->firstChild;
      while ($node instanceof \DOMNode) {
        if ($node instanceof Element) {
          return $node;
        }
        $node = $node->nextSibling;
      }
      return NULL;
    }

    /**
     * Returns the last element child node
     * @return Element|NULL
     */
    public function getLastElementChild() {
      if ($this instanceof Document) {
        return $this->documentElement;
      }
      /** @var \DOMNode $this */
      $node = $this->lastChild;
      while ($node instanceof \DOMNode) {
        if ($node instanceof Element) {
          return $node;
        }
        $node = $node->previousSibling;
      }
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