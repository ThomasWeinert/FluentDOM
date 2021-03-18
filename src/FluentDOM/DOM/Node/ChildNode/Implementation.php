<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
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
  use FluentDOM\Exceptions\UnattachedNode;

  trait Implementation {

    /**
     * Removes a node from its parent, returns the node
     * @return void
     */
    public function remove(): void {
      if ($this instanceof \DOMNode && $this->parentNode instanceof \DOMNode) {
        $this->parentNode->removeChild($this);
      }
    }

    /**
     * Insert nodes before a node.
     *
     * @param \DOMNode|\DOMNodeList|string ...$nodes
     * @throws UnattachedNode
     */
    public function before(...$nodes): void {
      /** @var \DOMNode|Implementation $this */
      if (
        (
          $this->parentNode instanceof \DOMElement ||
          $this->parentNode instanceof \DOMDocument
        ) &&
        ($fragment = MutationMacro::expand($this, ...$nodes))
      ) {
        $this->parentNode->insertBefore($fragment, $this);
      }
    }

    /**
     * Insert nodes after a node.
     *
     * @param \DOMNode|\DOMNodeList|string ...$nodes
     * @throws UnattachedNode
     */
    public function after(...$nodes): void {
      /** @var \DOMNode|Implementation $this */
      if (
        (
          $this->parentNode instanceof \DOMElement ||
          $this->parentNode instanceof \DOMDocument
        ) &&
        ($fragment = MutationMacro::expand($this, ...$nodes))
      ) {
        if ($this->nextSibling instanceof \DOMNode) {
          $this->parentNode->insertBefore($fragment, $this->nextSibling);
        } else {
          $this->parentNode->appendChild($fragment);
        }
      }
    }

    /**
     * Replace a node with on or more other nodes,
     * returns the replaced node.
     *
     * @param \DOMNode|\DOMNodeList $nodes
     * @throws UnattachedNode
     */
    public function replaceWith(...$nodes): void {
      $this->before(...$nodes);
      $this->remove();
    }

    /**
     * @param \DOMNode|\DOMNodeList $nodes
     * @return $this|\DOMNode
     * @throws UnattachedNode
     * @deprecated
     */
    public function replace($nodes) {
      $this->replaceWith($nodes);
      return $this;
    }
  }
}
