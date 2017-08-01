<?php

namespace FluentDOM\DOM\Node\ChildNode {

  use FluentDOM\DOM\Node\MutationMacro;

  trait Implementation {

    /**
     * Removes a node from its parent, returns the node
     *
     * @return $this|\DOMNode
     */
    public function remove(): \DOMNode {
      /** @var \DOMNode|Implementation $this */
      if ($this->parentNode instanceof \DOMNode) {
        $this->parentNode->removeChild($this);
      }
      return $this;
    }

    /**
     * Insert nodes before a node.
     *
     * @param \DOMNode|\DOMNodeList|NULL $nodes
     */
    public function before($nodes) {
      /** @var \DOMNode|Implementation $this */
      if (
        (
          $this->parentNode instanceof \DOMElement ||
          $this->parentNode instanceof \DOMDocument
        ) &&
        ($nodes = MutationMacro::expand($this->ownerDocument, $nodes))
      ) {
        $this->parentNode->insertBefore($nodes, $this);
      }
    }

    /**
     * Insert nodes after a node.
     *
     * @param \DOMNode|\DOMNodeList|NULL $nodes
     */
    public function after($nodes) {
      /** @var \DOMNode|Implementation $this */
      if (
        (
          $this->parentNode instanceof \DOMElement ||
          $this->parentNode instanceof \DOMDocument
        ) &&
        ($nodes = MutationMacro::expand($this->ownerDocument, $nodes))
      ) {
        if ($this->nextSibling instanceof \DOMNode) {
          $this->parentNode->insertBefore($nodes, $this->nextSibling);
        } else {
          $this->parentNode->appendChild($nodes);
        }
      }
    }

    /**
     * Replace a node with on or more other nodes,
     * returns the replaced node.
     *
     * @param \DOMNode|\DOMNodeList $nodes
     * @return $this|\DOMNode
     */
    public function replace($nodes): \DOMNode {
      $this->before($nodes);
      return $this->remove();
    }
  }
}