<?php

namespace FluentDOM\DOM\Node {

  abstract class MutationMacro {

    public static function expand(\DOMNode $context, $nodes) {
      $document = $context instanceof \DOMDocument ? $context : $context->ownerDocument;
      $result = $document->createDocumentFragment();
      if (!self::isTraversableOfNodes($nodes)) {
        $nodes = [$nodes];
      }
      foreach ($nodes as $node) {
        if ($node instanceof \DOMNode) {
          self::add($result, $node);
        } elseif (self::isStringCastable($node)) {
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
     * @param mixed $value
     * @return bool
     */
    private static function isStringCastable($value): bool {
      return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private static function isTraversableOfNodes($value): bool {
      return (
        !($value instanceof \DOMNode) &&
        ($value instanceof \Traversable || is_array($value))
      );
    }

    /**
     * @param \DOMDocumentFragment $target
     * @param \DOMNode $node
     */
    private static function add(\DOMDocumentFragment $target, \DOMNode $node) {
      if ($node instanceof \DOMDocument) {
        if ($node->documentElement instanceof \DOMElement) {
          $target->appendChild($target->ownerDocument->importNode($node->documentElement, TRUE));
        }
      } elseif ($node->ownerDocument !== $target->ownerDocument) {
        $target->appendChild($target->ownerDocument->importNode($node, TRUE));
      } else {
        $target->appendChild($node->cloneNode(TRUE));
      }
    }
  }
}