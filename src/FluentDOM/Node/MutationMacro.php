<?php

namespace FluentDOM\Node {

  abstract class MutationMacro {

    public static function expand(\DOMNode $context, $nodes) {
      /** @var \DOMDocumentFragment $result */
      $document = $context instanceof \DOMDocument ? $context : $context->ownerDocument;
      $result = $document->createDocumentFragment();
      if (
        $nodes instanceof \DOMNode ||
        !($nodes instanceof \Traversable || is_array($nodes))
      ) {
        $nodes = [$nodes];
      };
      foreach ($nodes as $node) {
        if ($node instanceof \DOMNode) {
          self::add($result, $node);
        } elseif (is_scalar($node) || (is_object($node) && method_exists($node, '__toString'))) {
          $result->appendChild($document->createTextNode((string)$node));
        } else {
          throw new \InvalidArgumentException(
            'Argument needs to be a dom node or castable into a string'
          );
        }
      }
      return $result->childNodes->length > 0 ? $result : NULL;
    }

    /**
     * @param \DOMDocumentFragment $target
     * @param \DOMNode $node
     */
    private static function add($target, $node) {
      if ($node instanceof \DOMDocument) {
        if ($node->documentElement instanceof \DOMElement) {
          $target->appendChild($target->ownerDocument->importNode($node->documentElement, TRUE));
        }
      } elseif ($node->ownerDocument != $target->ownerDocument) {
        $target->appendChild($target->ownerDocument->importNode($node, TRUE));
      } else {
        $target->appendChild($node->cloneNode(TRUE));
      }
    }
  }
}