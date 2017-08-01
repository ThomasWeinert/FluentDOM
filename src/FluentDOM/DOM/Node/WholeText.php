<?php
/**
 * Add the `replaceWholeText()` method. To the text node classes.
 *
 * https://www.w3.org/TR/DOM-Level-3-Core/core.html#Text3-replaceWholeText
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Document;

  // @codeCoverageIgnoreStart
  if (!defined('LIBXML_NO_MODIFICATION_ALLOWED_ERR')) {
    define('LIBXML_NO_MODIFICATION_ALLOWED_ERR',  7);
  }
  // @codeCoverageIgnoreEnd

  /**
   * Add the `replaceWholeText()` method. To the text node classes.
   *
   * https://www.w3.org/TR/DOM-Level-3-Core/core.html#Text3-replaceWholeText
   *
   * @property Document $ownerDocument
   * @property \DOMNode $previousSibling
   * @property \DOMNode $nextSibling
   * @property \DOMNode $parentNode
   */
  trait WholeText {

    /**
     * @param string $content
     * @return $this|NULL
     * @throws \DOMException
     */
    public function replaceWholeText($content) {
      /** @var \FluentDOM\DOM\Text|\FluentDOM\DOM\CdataSection $this */
      $content = (string)$content;
      $canReplaceEntity = function(\DOMEntityReference $reference) use (&$canReplaceEntity) {
        foreach ($reference->firstChild->childNodes as $childNode) {
          $canReplace = FALSE;
          if ($childNode instanceof \DOMEntityReference) {
            $canReplace = $canReplaceEntity($childNode);
          } elseif (
            $childNode instanceof \DOMCharacterData
          ) {
            $canReplace = TRUE;
          }
          if (!$canReplace) {
            return FALSE;
          }
        }
        return TRUE;
      };
      $replaceNode = function(\DOMNode $node = NULL) use ($canReplaceEntity) {
        if (
          $node instanceof \DOMNode &&
          !(
            $node instanceof \DOMElement ||
            $node instanceof \DOMComment ||
            $node instanceof \DOMProcessingInstruction
          ) &&
          $node->parentNode instanceof \DOMNode
        ) {
          if (
            $node instanceof \DOMEntityReference &&
            !$canReplaceEntity($node)
          ) {
            throw new \DOMException(LIBXML_NO_MODIFICATION_ALLOWED_ERR);
          }
          return TRUE;
        }
        return FALSE;
      };
      $fragment = $this->ownerDocument->createDocumentFragment();
      $iterate = function($start, \Closure $getNext) use ($fragment, $replaceNode) {
        if ($parent = $this->parentNode) {
          /** @noinspection PhpParamsInspection */
          $current = $getNext($start);
          while (($current instanceof \DOMNode) && $replaceNode($current)) {
            if ($current instanceof \DOMEntityReference) {
              $fragment->appendChild($current);
            } else {
              $parent->removeChild($current);
            }
            /** @noinspection PhpParamsInspection */
            $current = $getNext($start);
          }
        }
      };
      $iterate($this, function(\DOMNode $node) { return $node->previousSibling; } );
      $iterate($this, function(\DOMNode $node) { return $node->nextSibling; } );
      if ($content === '') {
        if ($this->parentNode instanceof \DOMNode) {
          $this->parentNode->removeChild($this);
        }
        $this->textContent = '';
        return NULL;
      }
      $this->textContent = $content;
      return $this;
    }
  }
}