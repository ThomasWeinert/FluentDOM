<?php

namespace FluentDOM {

  class Xpath extends \DOMXPath {

    public function registerNamespace($prefix, $namespace) {
      parent::registerNamespace($prefix, $namespace);
      if ($this->document instanceOf Document &&
          $this->document->getNamespace($prefix) !== $namespace) {
        $this->document->registerNameSpace($prefix, $namespace);
      }
    }
  }
}