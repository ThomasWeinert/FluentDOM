<?php

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Node;

  interface ChildNode extends Node {

    public function remove():\DOMNode;
    public function before($nodes);
    public function after($nodes);
    public function replace($nodes):\DOMNode;
  }
}