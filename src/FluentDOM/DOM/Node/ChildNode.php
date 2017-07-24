<?php

namespace FluentDOM\DOM\Node {

  interface ChildNode {

    function remove();
    function before($nodes);
    function after($nodes);
    function replace($nodes);
  }
}