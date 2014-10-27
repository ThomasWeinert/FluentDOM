<?php

namespace FluentDOM\Node {

  interface ChildNode {

    function remove();
    function before($nodes);
    function after($nodes);
    function replace($nodes);
  }
}