<?php
/**
 * Abstract utility class that provides several constraints/validations
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Utility {

  /**
   * Abstract utility class that provides several constraints/validations
   */
  abstract class Constraints {


    /**
     * Check if the DOMNode is DOMElement or DOMText with content.
     * It returns the node or NULL.
     *
     * @param mixed $node
     * @param bool $ignoreTextNodes
     * @return \DOMElement|\DOMText|\DOMCdataSection
     */
    public static function filterNode($node, $ignoreTextNodes = FALSE) {
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
     * @param mixed $node
     * @param string $message
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function assertNode($node, $message = 'DOMNode expected, got: %s.'): bool {
      if (!($node instanceof \DOMNode)) {
        throw new \InvalidArgumentException(
          sprintf(
            $message,
            is_object($node) ? get_class($node) : gettype($node)
          )
        );
      }
      return TRUE;
    }

    /**
     * Check if $elements is a traversable node list. It returns
     * the $elements or NULL
     *
     * @param mixed $elements
     * @return \Traversable|array
     */
    public static function filterNodeList($elements) {
      if ($elements instanceof \Traversable ||
          is_array($elements)) {
        return empty($elements) ? new \EmptyIterator() : $elements;
      }
      return NULL;
    }

    /**
     * check if parameter is a valid callback function. It returns
     * the callable or NULL.
     *
     * If $silent is disabled, an exception is thrown for invalid callbacks
     *
     * @param mixed $callback
     * @param bool $allowGlobalFunctions
     * @param bool $silent (no InvalidArgumentException)
     * @throws \InvalidArgumentException
     * @return callable|NULL
     */
    public static function filterCallable($callback, $allowGlobalFunctions = FALSE, $silent = TRUE) {
      if ($callback instanceof \Closure) {
        return $callback;
      }
      if (
        (is_string($callback) && $allowGlobalFunctions) ||
        self::filterCallableArray($callback)
      ) {
        return is_callable($callback) ? $callback : NULL;
      }
      if ($silent) {
        return NULL;
      }
      throw new \InvalidArgumentException('Invalid callback argument');
    }

    /**
     * Return TRUE if the $callback is an array that can be an
     *
     * @param mixed $callback
     * @return bool
     */
    private static function filterCallableArray($callback) {
      return (
        is_array($callback) &&
        count($callback) === 2 &&
        (is_object($callback[0]) || is_string($callback[0])) &&
        is_string($callback[1])
      );
    }

    /**
     * Check options bitmask for an option
     *
     * @param int $options
     * @param int $option
     * @return bool
     */
    public static function hasOption($options, $option): bool {
      return ($options & $option) === $option;
    }
  }
}