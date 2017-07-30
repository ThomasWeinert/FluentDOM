<?php

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Element;

  /**
   * Interface ParentNode
   * @property Element $firstElementChild
   * @property Element $lastElementChild
   */
  interface ParentNode extends QuerySelector {

    public function prepend($nodes);

    public function append($nodes);
  }
}