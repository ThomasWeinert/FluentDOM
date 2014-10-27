<?php

namespace FluentDOM\Node {

  use FluentDOM\Element;

  /**
   * Interface NonDocumentTypeChildNode
   * @property Element $nextElementSibling
   * @property Element $previousElementSibling
   */
  interface NonDocumentTypeChildNode {

  }

  trait NonDocumentTypeChildNodeImplementation {

    public function getNextElementSibling() {
      $node = $this;
      while ($node->nextSibling instanceof \DOMNode) {
        if ($node->nextSibling instanceof Element) {
          return $node->nextSibling;
        }
        $node = $node->nextSibling;
      }
      return NULL;
    }

    public function getPreviousElementSibling() {
      $node = $this;
      while ($node->previousSibling instanceof \DOMNode) {
        if ($node->previousSibling instanceof Element) {
          return $node->previousSibling;
        }
        $node = $node->previousSibling;
      }
      return NULL;
    }

  }

  trait NonDocumentTypeChildNodePropertyImplementation  {

    use NonDocumentTypeChildNodeImplementation;

    public function __get($name) {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling();
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling();
      }
      return $this->$name;
    }

    public function __set($name, $value) {
      switch ($name) {
      case 'nextElementSibling' :
      case 'previousElementSibling' :
        throw new \BadMethodCallException(
          sprintf(
            'Can not write readonly property %s::$%s.',
            get_class($this), $name
          )
        );
      }
      $this->$name = $value;
      return TRUE;
    }
  }

}