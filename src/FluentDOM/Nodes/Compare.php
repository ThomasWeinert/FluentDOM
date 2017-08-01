<?php

namespace FluentDOM\Nodes {

  class Compare {

    private $_xpath ;
    private $_document;
    private $_cache = [];

    public function __construct(\DOMXPath $xpath) {
      $this->_xpath = $xpath;
      $this->_document = $xpath->document;
    }

    public function __invoke($one, $two): int {
      if ($one === $two) {
        return 0;
      }
      if (
        $one === $this->_document->documentElement ||
        $one === $two->previousSibling ||
        $one === $two->parentNode
      ) {
        return -1;
      }
      if (
        $two === $this->_document->documentElement ||
        $two === $one->previousSibling ||
        $two === $one->parentNode) {
        return 1;
      }
      return $this->getPosition($one) - $this->getPosition($two);
    }

    private function getPosition(\DOMNode $node): int {
      $hash = spl_object_hash($node);
      if (!array_key_exists($hash, $this->_cache)) {
        $this->_cache[$hash] = (int)$this->_xpath->evaluate(
          'count(ancestor-or-self::node()/preceding::node()) + count(ancestor::node())', $node
        );
      }
      return $this->_cache[$hash];
    }
  }
}

