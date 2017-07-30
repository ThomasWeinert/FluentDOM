<?php

namespace FluentDOM\DOM\Node {

  interface ChildNode {

    public function remove():\DOMNode;
    public function before($nodes);
    public function after($nodes);
    public function replace($nodes):\DOMNode;
  }
}