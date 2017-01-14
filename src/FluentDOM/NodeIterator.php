<?php

namespace FluentDOM {

  class NodeIterator {

    /**
     * @var \DOMNode
     */
    private $_root;

    /**
     * @var int
     */
    private $_whatToShow;

    /**
     * @var NodeFilter
     */
    private $_filter;

    /**
     * @var \DOMNode
     */
    private $_referenceNode;

    /**
     * @var bool
     */
    private $_pointerBeforeReferenceNode = TRUE;

    /**
     * NodeIterator constructor.
     * @param \DOMNode $root
     * @param int $whatToShow
     * @param NodeFilter $filter
     */
    public function __construct(\DOMNode $root, $whatToShow, NodeFilter $filter) {
      $this->_root = $root;
      $this->_referenceNode = $this->_getNextNode($root);
      $this->_whatToShow = $whatToShow;
      $this->_filter = $filter;
    }

    public function __isset($name) {
      switch ($name) {
      case 'root' :
      case 'whatToShow' :
      case 'filter' :
        return TRUE;
      }
      return FALSE;
    }

    public function __get($name) {
      switch ($name) {
      case 'root' :
      case 'whatToShow' :
      case 'filter' :
      case 'referenceNode' :
      case 'pointerBeforeReferenceNode' :
        return $this->{'_'.$name};
      }
      return FALSE;
    }

    public function __set($name, $value) {
      throw new \LogicException('Can not set property '.$name);
    }

    public function __unset($name) {
      throw new \LogicException('Can not unset property '.$name);
    }

    /**
     * Get the previous node in the 'Collection'
     * @todo filter nodes according to $whatToShow
     *
     * @param \DOMNode $referenceNode
     * @return \DOMNode|null
     */
    private function _getPreviousNode(\DOMNode $referenceNode) {
      if ($referenceNode->previousSibling instanceof \DOMNode) {
        return $referenceNode->previousSibling;
      }
      if ($referenceNode->parentNode === $this->_root) {
        return NULL;
      }
      $node = $referenceNode->parentNode;
      while ($node->lastChild) {
        $node = $node->lastChild;
      }
      return $referenceNode->parentNode;
    }

    /**
     * Get the next node in the 'Collection'
     * @todo filter nodes according to $whatToShow
     *
     * @param \DOMNode $referenceNode
     * @return \DOMNode|null
     */
    private function _getNextNode(\DOMNode $referenceNode) {
      if ($referenceNode->firstChild instanceof \DOMNode) {
        return $referenceNode->firstChild;
      } elseif ($referenceNode->nextSibling instanceof \DOMNode) {
        return $referenceNode->nextSibling;
      }
      if ($referenceNode->parentNode === $this->_root) {
        return NULL;
      }
      $current = $referenceNode->parentNode;
      while ($current) {
        if ($current->nextSibling) {
          return $current->nextSibling;
        }
        $current = $current->parentNode;
      }
      return NULL;
    }

    public function previousNode() {
      $node = $this->_referenceNode;
      do {
        if (!$this->_pointerBeforeReferenceNode) {
          $this->_pointerBeforeReferenceNode = TRUE;
        } else {
          $node = $this->_getPreviousNode($node);
        }
        if (
          $node instanceof \DOMNode &&
          $this->_filter->acceptNode($node) === NodeFilter::FILTER_ACCEPT
        ) {
          return $this->_referenceNode = $node;
        }
      } while ($node instanceof \DOMNode);
      return NULL;
    }

    public function nextNode() {
      $node = $this->_referenceNode;
      do {
        if ($this->_pointerBeforeReferenceNode) {
          $this->_pointerBeforeReferenceNode = FALSE;
        } else {
          $node = $this->_getNextNode($node);
        }
        if (
          $node instanceof \DOMNode &&
          $this->_filter->acceptNode($node) === NodeFilter::FILTER_ACCEPT
        ) {
          return $this->_referenceNode = $node;
        }
      } while ($node instanceof \DOMNode);
      return NULL;
    }
  }
}