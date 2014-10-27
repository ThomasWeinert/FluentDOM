<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;

  abstract class MutationMacro {

    public static function expand(Document $document, $nodes) {
      /** @var \DOMNode|ChildNodeImplementation $this */
      /** @var \DOMDocumentFragment $result */
      $result = $document->createDocumentFragment();
      if (
        $nodes instanceof \DOMNode ||
        !($nodes instanceof \Traversable || is_array($nodes))
      ) {
        $nodes = [$nodes];
      };
      foreach ($nodes as $node) {
        if ($node instanceof \DOMNode && !($nodes instanceof \DOMDocument)) {
          $result->appendChild($node);
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