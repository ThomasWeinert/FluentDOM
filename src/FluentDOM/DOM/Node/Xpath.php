<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
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
     *
     * @param string $expression
     * @param Node|\DOMNode|NULL $context
     * @return string|float|\DOMNodeList|Node[]
     */
    public function evaluate(string $expression, Node $context = NULL) {
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
     *
     * @param string $expression
     * @return string|float|\DOMNodeList|Node[]
     */
    public function __invoke(string $expression) {
      return $this->evaluate(
        $expression, $this instanceof \DOMNode ? $this : NULL
      );
    }
  }
}
