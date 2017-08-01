<?php
/**
 * Add a `evaluate()` method to execute an Xpath expression in the context of the node and
 * make the node a functor.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

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
     * @param \DOMNode $context
     * @return string|float|\DOMNodeList|Node[]
     */
    public function evaluate(string $expression, \DOMNode $context = NULL) {
      /** @var Document $document */
      /** @var \DOMNode $this */
      $document = $this instanceof \DOMDocument
        ? $this
        : $this->ownerDocument;
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