<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Utility {

  /**
   * Abstract utility class that provides several constraints/validations
   */
  abstract class Constraints {

    /**
     * Check if the DOMNode is DOMElement or DOMText with content.
     * It returns the node or NULL.
     */
    public static function filterNode(
      mixed $node, bool $ignoreTextNodes = FALSE
    ): \DOMElement|\DOMCharacterData|NULL {
      if (
        $node instanceof \DOMElement ||
        (
          !$ignoreTextNodes &&
          (
            $node instanceof \DOMCdataSection ||
            (
              $node instanceof \DOMText &&
              !$node->isWhitespaceInElementContent()
            )
          )
        )
      ) {
        return $node;
      }
      return NULL;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function assertNode(
      mixed $node, string $message = 'DOMNode expected, got: %s.'
    ): bool {
      if (!($node instanceof \DOMNode)) {
        throw new \InvalidArgumentException(
          \sprintf(
            $message,
            \is_object($node) ? \get_class($node) : \gettype($node)
          )
        );
      }
      return TRUE;
    }

    /**
     * @param string|string[] $classes
     */
    public static function assertNodeClass(
      \DOMNode $node, string|array $classes, string $message = 'Unexpected node type: %s'
    ): bool {
      if (!is_array($classes)) {
        $classes = [$classes];
      }
      foreach ($classes as $className) {
        if ($node instanceof $className) {
          return TRUE;
        }
      }
      throw new \LogicException(
        sprintf($message, get_class($node))
      );
    }

    /**
     * Check if $elements is a traversable node list. It returns
     * the $elements or NULL
     */
    public static function filterNodeList(mixed $elements): ?iterable {
      if (is_iterable($elements)) {
        return empty($elements) ? new \EmptyIterator() : $elements;
      }
      return NULL;
    }

    /**
     * check if parameter is a valid callback function. It returns
     * the a Closure or NULL.
     *
     * If $silent is disabled, an exception is thrown for invalid callbacks
     *
     * @throws \InvalidArgumentException
     */
    public static function filterCallable(
      mixed $callback,
      bool $allowGlobalFunctions = FALSE,
      bool $silent = TRUE
    ): ?\Closure {
      if ($callback instanceof \Closure) {
        return $callback;
      }
      if (\is_string($callback) && $allowGlobalFunctions) {
        return \Closure::fromCallable($callback);
      }
      if ($closure = self::filterCallableArray($callback)) {
        return $closure;
      }
      if ($silent) {
        return NULL;
      }
      throw new \InvalidArgumentException('Invalid callback argument');
    }

    /**
     * Return the $callback wrapped into a Closure  if the $callback is an array that
     * can be a callable.
     */
    private static function filterCallableArray(mixed $callback): ?\Closure {
      return (
        \is_array($callback) &&
        \count($callback) === 2 &&
        (\is_object($callback[0]) || \is_string($callback[0])) &&
        \is_string($callback[1])
      ) ? \Closure::fromCallable($callback) : NULL;
    }

    /**
     * Check options bitmask for an option
     */
    public static function hasOption(int $options, int $option): bool {
      return ($options & $option) === $option;
    }
  }
}
