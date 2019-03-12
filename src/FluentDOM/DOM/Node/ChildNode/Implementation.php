<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM\Node\ChildNode {

  use FluentDOM\DOM\Node\MutationMacro;

  trait Implementation {

    /**
     * Removes a node from its parent, returns the node
     *
     * @return $this|\DOMNode
     */
    public function remove(): \DOMNode {
      if ($this instanceof \DOMNode && $this->parentNode instanceof \DOMNode) {
        $this->parentNode->removeChild($this);
      }
      return $this;
    }

    /**
     * Insert nodes before a node.
     *
     * @param \DOMNode|\DOMNodeList|NULL $nodes
     */
    public function before($nodes) {
      /** @var \DOMNode|Implementation $this */
      if (
        (
          $this->parentNode instanceof \DOMElement ||
          $this->parentNode instanceof \DOMDocument
        ) &&
        ($nodes = MutationMacro::expand($this->ownerDocument, $nodes))
      ) {
        $this->parentNode->insertBefore($nodes, $this);
      }
    }

    /**
     * Insert nodes after a node.
     *
     * @param \DOMNode|\DOMNodeList|NULL $nodes
     */
    public function after($nodes) {
      /** @var \DOMNode|Implementation $this */
      if (
        (
          $this->parentNode instanceof \DOMElement ||
          $this->parentNode instanceof \DOMDocument
        ) &&
        ($nodes = MutationMacro::expand($this->ownerDocument, $nodes))
      ) {
        if ($this->nextSibling instanceof \DOMNode) {
          $this->parentNode->insertBefore($nodes, $this->nextSibling);
        } else {
          $this->parentNode->appendChild($nodes);
        }
      }
    }

    /**
     * Replace a node with on or more other nodes,
     * returns the replaced node.
     *
     * @param \DOMNode|\DOMNodeList $nodes
     * @return $this|\DOMNode
     */
    public function replaceWith($nodes): \DOMNode {
      $this->before($nodes);
      return $this->remove();
    }

    /**
     * @param  \DOMNode|\DOMNodeList $nodes
     * @return $this|\DOMNode
     * @deprecated re
     */
    public function replace($nodes) {
      return $this->replaceWith($nodes);
    }
  }
}
