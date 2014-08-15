<?php
/**
 * Cast a DOMNode into a string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Node {

  use FluentDOM\Document;

  /**
   * Cast a DOMNode into a string
   *
   * @property string $nodeValue
   * @property Document $ownerDocument
   */
  trait Xpath {

    /**
     * Evaluate an xpath expression in the context of this
     * element.
     *
     * @param string $expression
     * @param \DOMNode $context
     * @return mixed
     */
    public function evaluate($expression, \DOMNode $context = NULL) {
      /** @var Document $document */
      $document = $this instanceof Document
        ? $this
        : $this->ownerDocument;
      return $document->xpath()->evaluate(
        $expression, isset($context) ? $context : $this
      );
    }

    /**
     * Allow to call evaluate() by using the node as object
     *
     * @param string $expression
     * @return mixed
     */
    public function __invoke($expression) {
      return $this->evaluate(
        $expression, $this instanceof \DOMNode ? $this : NULL
      );
    }
  }
}