<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Implementation;
  use FluentDOM\Exceptions\UnattachedNode;

  /**
   * Class MutationMacro
   *
   * Converts a list of potential nodes into a single DOM node.
   *
   * @package FluentDOM\DOM\Node
   */
  abstract class MutationMacro {

    /**
     * @param \DOMNode $context
     * @param \DOMNode|string|string[]|\DOMNodeList ...$nodes
     * @return \DOMDocumentFragment|NULL
     * @throws UnattachedNode
     */
    public static function expand(\DOMNode $context, ...$nodes): ?\DOMDocumentFragment {
      $document = Implementation::getNodeDocument($context);
      $result = $document->createDocumentFragment();
      foreach ($nodes as $node) {
        if (NULL === $node) {
          continue;
        }
        if ($node instanceof \DOMNodeList || self::isTraversableOfNodes($node)) {
          foreach ($node as $childNode) {
            if ($childNode instanceof \DOMNode) {
              self::add($result, $childNode);
            } elseif (self::isStringCastable($childNode)) {
              $result->appendChild($document->createTextNode((string)$childNode));
            } else {
              throw new \InvalidArgumentException(
                'Argument needs to be a dom node or castable into a string'
              );
            }
          }
        } elseif ($node instanceof \DOMNode) {
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
      return \is_scalar($value) || (\is_object($value) && \method_exists($value, '__toString'));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private static function isTraversableOfNodes($value): bool {
      return (
        !($value instanceof \DOMNode) && \is_iterable($value)
      );
    }

    /**
     * @param \DOMDocumentFragment $target
     * @param \DOMNode $node
     */
    private static function add(\DOMDocumentFragment $target, \DOMNode $node): void {
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
