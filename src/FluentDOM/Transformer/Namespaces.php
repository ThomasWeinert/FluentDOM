<?php
/**
 * Superclass for the namespace transformers (optimize and replace)
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Transformer {

  use FluentDOM\Appendable;
  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\Utility\StringCastable;

  abstract class Namespaces implements \IteratorAggregate, Appendable, StringCastable {

    /**
     * @var Document
     */
    private $_document;

    /**
     * Add a node to the target node
     *
     * @param \DOMNode $target
     * @param \DOMNode $source
     */
    abstract protected function addNode(\DOMNode $target, \DOMNode $source);

    /**
     * @param \DOMNode $node
     */
    public function __construct(\DOMNode $node) {
      $document = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
      $this->_document = new Document($document->xmlVersion, $document->xmlEncoding);
      if ($node instanceof \DOMDocument) {
        foreach ($document->childNodes as $childNode) {
          $this->_document->appendChild(
            $this->_document->importNode($childNode, TRUE)
          );
        }
      } else {
        $this->_document->appendChild(
          $this->_document->importNode($node, TRUE)
        );
      }
    }

    /**
     * Create a document with optimized namespaces and return it as xml string
     *
     * @return string
     */
    public function __toString(): string {
      return $this->getDocument()->saveXML();
    }

    /**
     * Create and return a document with optimized namespaces.
     *
     * @return Document
     */
    public function getDocument(): Document {
      $document = new Document($this->_document->xmlVersion, $this->_document->xmlEncoding);
      foreach ($this->_document->childNodes as $node) {
        $this->addNode($document, $node);
      }
      return $document;
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      $document = $this->getDocument();
      return new \CallbackFilterIterator(
        new \IteratorIterator($document->childNodes),
        function (\DOMNode $node) {
          return $node instanceof \DOMElement;
        }
      );
    }

    /**
     * Append transformed nodes to another DOM
     *
     * @param Element $parentNode
     * @return Element|NULL|void
     */
    public function appendTo(Element $parentNode) {
      foreach ($this->_document->childNodes as $node) {
        if ($node instanceof \DOMElement) {
          $this->addNode($parentNode, $node);
        }
      }
    }
  }
}