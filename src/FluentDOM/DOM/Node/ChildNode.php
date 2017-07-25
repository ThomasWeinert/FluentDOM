<?php

namespace FluentDOM\DOM\Node {

  interface ChildNode {

    function remove():\DOMNode;
    function before($nodes);
    function after($nodes);
    function replace($nodes):\DOMNode;
  }
}