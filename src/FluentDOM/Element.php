<?php

namespace FluentDOM {

  class Element extends \DOMElement {

    public function appendElement($name, $content = '', array $attributes = NULL) {
      $this->appendChild(
        $node = $this->ownerDocument->createElement($name, $content, $attributes)
      );
      return $node;
    }
  }
}