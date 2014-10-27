<?php

namespace FluentDOM\Node {
  use FluentDOM\Document;
  use FluentDOM\Element;

  /**
   * Interface ParentNode
   * @property \FluentDOM\Element $firstElementChild
   * @property \FluentDOM\Element $lastElementChild
   */
  interface ParentNode {

    function prepend($nodes);

    function append($nodes);
  }
}