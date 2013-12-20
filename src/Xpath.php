<?php

namespace FluentDOM {

  class Xpath extends \DOMXPath {

    public function __construct(Document $document) {
      parent::__construct($document);
    }

    public function registerNamespace($prefix, $namespace) {
      if ($this->document->getNamespace($prefix) !== $namespace) {
        $this->document->registerNameSpace($prefix, $namespace);
      }
    }

  }
}