<?php

namespace FluentDOM\Node {

  interface QuerySelector {

    /**
     * @param string $selector
     * @return \FluentDOM\Element|NULL
     */
    function querySelector($selector);

    /**
     * @param string $selector
     * @return \DOMNodeList
     */
    function querySelectorAll($selector);
  }
}