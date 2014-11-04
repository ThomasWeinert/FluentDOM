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
        if ($node instanceof \DOMDocument) {
          if ($node->documentElement instanceof \DOMElement) {
            $result->appendChild($document->importNode($node->documentElement));
          }
        } elseif ($node instanceof \DOMNode) {
          if ($node->ownerDocument != $document) {
            $result->appendChild($document->importNode($node));
          } else {
            $result->appendChild($node->cloneNode(TRUE));
          }
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
  }
}