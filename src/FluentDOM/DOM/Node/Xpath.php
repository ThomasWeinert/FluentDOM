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

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Node;

  /**
   * Add a `evaluate()` method to execute an Xpath expression in the context of the node and
   * make the node a functor.
   *
   * @property Document $ownerDocument
   */
  trait Xpath {

    /**
     * Evaluate an xpath expression in the context of this
     * element.
     */
    public function evaluate(
      string $expression, Node $context = NULL
    ): string|float|bool|\DOMNodeList {
      $document = $this instanceof Document
        ? $this
        : $this->ownerDocument;
      if (!$document instanceof Document) {
        throw new \LogicException('Node is not owned by a document.');
      }
      return $document->xpath()->evaluate(
        $expression, $context ?? $this
      );
    }

    /**
     * Allow to call evaluate() by using the node as object
     */
    public function __invoke(string $expression): string|float|bool|\DOMNodeList {
      return $this->evaluate(
        $expression, $this instanceof \DOMNode ? $this : NULL
      );
    }
  }
}
