<?php

namespace FluentDOM\Node {

  use FluentDOM\Element;

  /**
   * Interface ParentNode
   * @property Element $firstElementChild
   * @property Element $lastElementChild
   */
  interface ParentNode {

    function prepend($nodes);

    function append($nodes);
  }
}