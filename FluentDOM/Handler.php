<?php
/**
* FluentDOMHandler provides dom manipulation functions
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2010 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
*/

/**
* FluentDOMHandler provides dom manipulation functions
*
* @package FluentDOM
*/
class FluentDOMHandler {

  /**
  * Insert nodes after the target node.
  * @param DOMNode $targetNode
  * @param array|DOMNodeList|FluentDOM $contentNodes
  */
  public static function insertNodesAfter($targetNode, $contentNodes) {
    $result = array();
    if (isset($targetNode->parentNode) &&
        !empty($contentNodes)) {
      $beforeNode = $targetNode->nextSibling;
      foreach ($contentNodes as $contentNode) {
        $result[] = $targetNode->parentNode->insertBefore(
          $contentNode->cloneNode(TRUE), $beforeNode
        );
      }
    }
    return $result;
  }

  /**
  * Insert nodes before the target node.
  * @param DOMNode $targetNode
  * @param array|DOMNodeList|FluentDOM $contentNodes
  */
  public static function insertNodesBefore($targetNode, $contentNodes) {
    $result = array();
    if (isset($targetNode->parentNode) &&
        !empty($contentNodes)) {
      foreach ($contentNodes as $contentNode) {
        $result[] = $targetNode->parentNode->insertBefore(
          $contentNode->cloneNode(TRUE), $targetNode
        );
      }
    }
    return $result;
  }

  /**
  * Append nodes into target.
  *
  * @param DOMNode $targetNode
  * @param array|DOMNodeList|FluentDOM $contentNodes
  */
  public static function appendChildren($targetNode, $contentNodes) {
    $result = array();
    if ($targetNode instanceof DOMElement) {
      foreach ($contentNodes as $contentNode) {
        if ($contentNode instanceof DOMElement ||
            $contentNode instanceof DOMText) {
          $result[] = $targetNode->appendChild($contentNode->cloneNode(TRUE));
        }
      }
    }
    return $result;
  }

  /**
  * Insert nodes into target as first childs.
  *
  * @param DOMNode $targetNode
  * @param array|DOMNodeList|FluentDOM $contentNodes
  */
  public static function insertChildrenBefore($targetNode, $contentNodes) {
    $result = array();
    if ($targetNode instanceof DOMElement) {
      $firstChild = $targetNode->hasChildNodes() ? $targetNode->childNodes->item(0) : NULL;
      foreach ($contentNodes as $contentNode) {
        if ($contentNode instanceof DOMElement ||
            $contentNode instanceof DOMText) {
          $result[] = $targetNode->insertBefore(
            $contentNode->cloneNode(TRUE),
            $firstChild
          );
        }
      }
    }
    return $result;
  }
}