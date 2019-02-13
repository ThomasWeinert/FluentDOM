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

namespace FluentDOM\Nodes {

  use FluentDOM\Utility\Constraints;

  /**
   * Provides several extended manipulation functions for a DOMNode/DOMElement.
   */
  class Modifier {

    /**
     * @var \DOMNode
     */
    private $_node;

    /**
     * @param \DOMNode $node
     */
    public function __construct(\DOMNode $node) {
      $this->_node = $node;
    }

    /**
     * @return \DOMNode
     */
    public function getNode(): \DOMNode {
      return $this->_node;
    }

    /**
     * @return \DOMNode
     */
    private function getParentNode(): \DOMNode {
      return $this->_node->parentNode;
    }

    /**
     * Append to content nodes to the target nodes.
     *
     * @param array|\Traversable $contentNodes
     * @return array new nodes
     */
    public function appendChildren($contentNodes): array {
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
     *
     * @param array|\Traversable $contentNodes
     * @return array new nodes
     */
    public function replaceChildren($contentNodes): array {
      $this->_node->nodeValue = '';
      return $this->appendChildren($contentNodes);
    }

    /**
     * Insert nodes into target as first children.
     *
     * @param array|\Traversable $contentNodes
     * @return array
     */
    public function insertChildrenBefore($contentNodes): array {
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
     * @param array|\Traversable $contentNodes
     * @return array
     */
    public function insertNodesAfter($contentNodes): array {
      $result = [];
      if ($this->_node instanceof \DOMNode && NULL !== $contentNodes) {
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
      }
      return $result;
    }

    /**
     * Insert nodes before the target node.
     *
     * @param array|\Traversable $contentNodes
     * @return array
     */
    public function insertNodesBefore($contentNodes): array {
      $result = [];
      if ($this->_node instanceof \DOMNode && !NULL !== $contentNodes) {
        foreach ($contentNodes as $contentNode) {
          /** @var \DOMNode $contentNode */
          $result[] = $this->getParentNode()->insertBefore(
            $contentNode->cloneNode(TRUE), $this->_node
          );
        }
      }
      return $result;
    }

    /**
     * @param array|\Traversable $contentNodes
     * @return \DOMNode
     */
    public function replaceNode($contentNodes): \DOMNode {
      $this->insertNodesBefore($contentNodes);
      return $this->getParentNode()->removeChild($this->getNode());
    }
  }
}
