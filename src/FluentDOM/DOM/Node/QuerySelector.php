<?php

namespace FluentDOM\DOM\Node {

  interface QuerySelector {

    /**
     * @param string $selector
     * @return \FluentDOM\DOM\Element|NULL
     */
    function querySelector($selector);

    /**
     * @param string $selector
     * @return \DOMNodeList
     */
    function querySelectorAll($selector);
  }
}