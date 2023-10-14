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

namespace FluentDOM\Nodes {

  use FluentDOM\Utility\Constraints;

  /**
   * Provides several extended manipulation functions for a DOMNode/DOMElement.
   */
  class Modifier {

    private \DOMNode $_node;

    public function __construct(\DOMNode $node) {
      $this->_node = $node;
    }

    public function getNode(): \DOMNode {
      return $this->_node;
    }

    private function getParentNode(): \DOMNode {
      return $this->_node->parentNode;
    }

    /**
     * Append to content nodes to the target nodes.
     */
    public function appendChildren(iterable $contentNodes): array {
      $result = [];
      if ($this->_node instanceof \DOMElement) {
        foreach ($contentNodes as $contentNode) {
          /** @var \DOMNode $contentNode */
          if (Constraints::filterNode($contentNode)) {
            $result[] = $this->_node->appendChild($contentNode->cloneNode(TRUE));
          }
        }
      }
      return $result;
    }

    /**
     * Replace the target node children with the content nodes
     */
    public function replaceChildren(iterable $contentNodes): array {
      $this->_node->nodeValue = '';
      return $this->appendChildren($contentNodes);
    }

    /**
     * Insert nodes into target as first children.
     */
    public function insertChildrenBefore(iterable $contentNodes): array {
      $result = [];
      if ($this->_node instanceof \DOMElement) {
        if (NULL !== $this->_node->firstChild) {
          $result = (new self($this->_node->firstChild))->insertNodesBefore($contentNodes);
        } else {
          $result = $this->appendChildren($contentNodes);
        }
      }
      return $result;
    }

    /**
     * Insert nodes after the target node.
     */
    public function insertNodesAfter(iterable $contentNodes): array {
      $result = [];
      $beforeNode = $this->_node->nextSibling ?: NULL;
      $hasContext = $beforeNode instanceof \DOMNode;
      foreach ($contentNodes as $contentNode) {
        /** @var \DOMNode $contentNode */
        if ($hasContext) {
          $result[] = $this->getParentNode()->insertBefore(
            $contentNode->cloneNode(TRUE), $beforeNode
          );
        } else {
          $result[] = $this->getParentNode()->appendChild(
            $contentNode->cloneNode(TRUE)
          );
        }
      }
      return $result;
    }

    /**
     * Insert nodes before the target node.
     */
    public function insertNodesBefore(iterable $contentNodes): array {
      $result = [];
      if ($this->_node instanceof \DOMNode) {
        foreach ($contentNodes as $contentNode) {
          /** @var \DOMNode $contentNode */
          $result[] = $this->getParentNode()->insertBefore(
            $contentNode->cloneNode(TRUE), $this->_node
          );
        }
      }
      return $result;
    }

    public function replaceNode(iterable $contentNodes): \DOMNode {
      $this->insertNodesBefore($contentNodes);
      return $this->getParentNode()->removeChild($this->getNode());
    }
  }
}
