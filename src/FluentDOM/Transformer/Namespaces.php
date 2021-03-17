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

namespace FluentDOM\Transformer {

  use FluentDOM\Appendable;
  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\DOM\Implementation;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Utility\StringCastable;

  /**
   * Superclass for the namespace transformers (optimize and replace)
   *
   * @package FluentDOM\Transformer
   */
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
     * @throws UnattachedNode
     */
    public function __construct(\DOMNode $node) {
      $document = Implementation::getNodeDocument($node);
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
     */
    public function appendTo(Element $parentNode): void {
      foreach ($this->_document->childNodes as $node) {
        if ($node instanceof \DOMElement) {
          $this->addNode($parentNode, $node);
        }
      }
    }
  }
}
