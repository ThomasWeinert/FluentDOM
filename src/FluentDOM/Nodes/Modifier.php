<?php
/**
 * Provides several extended manipulation functions for a DOMNode/DOMElement.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Nodes {

  use FluentDOM\Constraints;

  /**
   * Provides several extended manipulation functions for a DOMNode/DOMElement.
   */
  class Modifier {

    /**
     * @var \DOMNode
     */
    private $_node = NULL;

    /**
     * @param \DOMNode $node
     */
    public function __construct(\DOMNode $node) {
      $this->_node = $node;
    }

    /**
     * @return \DOMNode
     */
    public function getNode() {
      return $this->_node;
    }

    /**
     * @return \DOMNode
     */
    private function getParentNode() {
      return $this->_node->parentNode;
    }

    /**
     * Append to content nodes to the target nodes.
     *
     * @param array|\Traversable $contentNodes
     * @return array new nodes
     */
    public function appendChildren($contentNodes) {
      $result = array();
      if ($this->_node instanceof \DOMElement) {
        foreach ($contentNodes as $contentNode) {
          /** @var \DOMNode $contentNode */
          if (Constraints::isNode($contentNode)) {
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
    public function replaceChildren($contentNodes) {
      $this->_node->nodeValue = '';
      return $this->appendChildren($contentNodes);
    }

    /**
     * Insert nodes into target as first children.
     *
     * @param array|\Traversable $contentNodes
     * @return array
     */
    public function insertChildrenBefore($contentNodes) {
      $result = array();
      if ($this->_node instanceof \DOMElement) {
        if ($this->_node->firstChild instanceof \DOMNode) {
          $result = (new self($this->_node->firstChild))->insertNodesBefore($contentNodes);
        } else {
          $result = self::appendChildren($contentNodes);
        }
      }
      return $result;
    }

    /**
     * Insert nodes after the target node.
     * @param array|\Traversable $contentNodes
     * @return array
     */
    public function insertNodesAfter($contentNodes) {
      $result = array();
      if ($this->_node instanceof \DOMNode && !empty($contentNodes)) {
        $beforeNode = ($this->_node->nextSibling instanceof \DOMNode)
          ? $this->_node->nextSibling : NULL;
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
    public function insertNodesBefore($contentNodes) {
      $result = array();
      if ($this->_node instanceof \DOMNode && !empty($contentNodes)) {
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
    public function replaceNode($contentNodes) {
      $this->insertNodesBefore($contentNodes);
      return $this->getParentNode()->removeChild($this->getNode());
    }
  }
}