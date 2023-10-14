<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\DOM\Node\ParentNode {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\DOM\Node\MutationMacro;
  use FluentDOM\Exceptions\UnattachedNode;

  /**
   * @property-read \DOMNode $firstChild
   * @property-read \DOMNode $lastChild
   * @property-read \DOMNode $nextSibling
   * @property-read \DOMNode $previousSibling
   */
  trait Implementation {

    abstract public function insertBefore(\DOMNode $newChild, \DOMNode $refChild = NULL);

    abstract public function appendChild(\DOMNode $newChild);

    /**
     * Insert nodes before the first child node
     *
     * @param mixed $nodes
     * @throws UnattachedNode
     */
    public function prepend(...$nodes): void {
      /** @var \DOMNode|Implementation $this */
      if (
        $this->firstChild instanceof \DOMNode
        && ($nodes = MutationMacro::expand($this, ...$nodes))
      ) {
        $this->insertBefore($nodes, $this->firstChild);
      } else {
        $this->append(...$nodes);
      }
    }

    /**
     * Append nodes as children to the node itself
     *
     * @param mixed $nodes
     * @throws UnattachedNode
     */
    public function append(...$nodes): void {
      /** @var \DOMNode|Implementation $this */
      if ($nodes = MutationMacro::expand($this, ...$nodes)) {
        $this->appendChild($nodes);
      }
    }
  }
}
