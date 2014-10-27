<?php

namespace FluentDOM\Node {
  use FluentDOM\Document;
  use FluentDOM\Element;

  /**
   * Interface ParentNode
   * @property \FluentDOM\Element $firstElementChild
   * @property \FluentDOM\Element $lastElementChild
   */
  interface ParentNode {

    function prepend($nodes);

    function append($nodes);
  }

  trait ParentNodeImplementation {

    public function getFirstElementChild() {
      if ($this instanceof Document) {
        return $this->documentElement;
      }
      $node = $this->firstChild;
      while ($node->nextSibling instanceof \DOMNode) {
        if ($node->nextSibling instanceof Element) {
          return $node->nextSibling;
        }
        $node = $node->nextSibling;
      }
      return NULL;
    }

    public function getLastElementChild() {
      if ($this instanceof Document) {
        return $this->documentElement;
      }
      $node = $this->lastChild;
      while ($node->previousSibling instanceof \DOMNode) {
        if ($node->previousSibling instanceof Element) {
          return $node->previousSibling;
        }
        $node = $node->previousSibling;
      }
      return NULL;
    }

    public function prepend($nodes) {
      /** @var \DOMNode|ParentNodeImplementation $this */
      if (
        $this->firstChild instanceof \DOMNode
        && ($nodes = MutationMacro::expand($this->ownerDocument, $nodes))
      ) {
        $this->insertBefore($nodes, $this->firstChild);
      } else {
        $this->append($nodes);
      }
    }

    public function append($nodes) {
      /** @var \DOMNode|ParentNodeImplementation $this */
      if ($nodes = MutationMacro::expand($this->ownerDocument, $nodes)) {
        $this->appendChild($nodes, $this);
      }
    }
  }
  trait ParentNodePropertyImplementation {

    use ParentNodeImplementation;

    public function __get($name) {
      switch ($name) {
      case 'firstElementChild' :
        return $this->getFirstElementChild();
      case 'lastElementChild' :
        return $this->getLastElementChild();
      }
      return $this->$name;
    }

    public function __set($name, $value) {
      switch ($name) {
      case 'firstElementChild' :
      case 'lastElementChild' :
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