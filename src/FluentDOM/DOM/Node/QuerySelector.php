<?php

namespace FluentDOM\DOM\Node {

  interface QuerySelector {

    /**
     * @param string $selector
     * @return \FluentDOM\DOM\Element|NULL
     */
    public function querySelector(string $selector);

    /**
     * @param string $selector
     * @return \DOMNodeList
     */
    public function querySelectorAll(string $selector):\DOMNodeList;
  }
}