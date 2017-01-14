<?php

namespace FluentDOM {

  /**
   * An interface for objects that can be use with \FluentDOM\NodeIterator  and
   * FluentDOM\TreeWalker to traverse a DOM
   */
  interface NodeFilter {

    const FILTER_ACCEPT = 1;
    const FILTER_REJECT = 2;
    const FILTER_SKIP = 3;

    const SHOW_ALL = -1;
    const SHOW_ATTRIBUTE = 2;
    const SHOW_CDATA_SECTION = 8;
    const SHOW_COMMENT = 128;
    const SHOW_DOCUMENT = 256;
    const SHOW_DOCUMENT_FRAGMENT = 1024;
    const SHOW_DOCUMENT_TYPE = 512;
    const SHOW_ELEMENT = 1;
    const SHOW_ENTITY = 32;
    const SHOW_ENTITY_REFERENCE = 16;
    const SHOW_NOTATION = 2048;
    const SHOW_PROCESSING_INSTRUCTION = 64;
    const SHOW_TEXT = 4;

    /**
     * The method needs to return on of the FILTER_* class constants
     *
     * @param \DOMNode $node
     * @return int
     */
    public function acceptNode(\DOMNode $node);
  }
}