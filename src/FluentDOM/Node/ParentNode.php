<?php

namespace FluentDOM\Node {

  /**
   * Interface ParentNode
   * @property \FluentDOM\Element $firstElementChild
   * @property \FluentDOM\Element $lastElementChild
   */
  interface ParentNode {

    function prepend($nodes);

    function append($nodes);
  }

  trait ParentNodeImplementation {

    public function prepend($nodes) {
      /** @var \DOMNode|ParentNodeImplementation $this */
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
      /** @var \DOMNode|ParentNodeImplementation $this */
      if ($nodes = MutationMacro::expand($this->ownerDocument, $nodes)) {
        $this->appendChild($nodes, $this);
      }
    }
  }

}