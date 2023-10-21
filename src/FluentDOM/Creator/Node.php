<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Creator {

  use FluentDOM\Appendable;
  use FluentDOM\Creator;
  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Transformer\Namespaces\Optimize;

  /**
   * Internal class for the FluentDOM\Creator, please do not use directly
   *
   * @property-read Document $document
   * @property-read Element $node
   */
  class Node implements Appendable, \IteratorAggregate {

    private Document $_document;

    private \DOMElement $_node;

    private Creator $_creator;

    public function __construct(Creator $creator, Document $document, \DOMElement $node) {
      $this->_creator = $creator;
      $this->_document = $document;
      $this->_node = $node;
    }

    public function __isset(string $name): bool {
      return match ($name) {
        'document', 'node' => TRUE,
        default => FALSE,
      };
    }

    public function __get(string $name): \DOMElement|null|Document {
      return match ($name) {
        'document' => $this->getDocument(),
        'node' => $this->_node,
        default => NULL,
      };
    }

    /**
     * @throws \BadMethodCallException
     */
    public function __set(string $name, mixed $value): void {
      throw new \BadMethodCallException(
        \sprintf('%s is immutable.', \get_class($this))
      );
    }

    /**
     * @throws \BadMethodCallException
     */
    public function __unset(string $name): void {
      throw new \BadMethodCallException(
        \sprintf('%s is immutable.', \get_class($this))
      );
    }

    public function getDocument(): Document {
      $document = clone $this->_document;
      $document->appendChild($document->importNode($this->_node, TRUE));
      if ($this->_creator->optimizeNamespaces) {
        $document = (new Optimize($document))->getDocument();
        $document->formatOutput = $this->_document->formatOutput;
      }
      return $document;
    }

    public function __toString(): string {
      return $this->getDocument()->saveXML() ?: '';
    }

    public function appendTo(Element $parentNode): void {
      if (!$parentNode->ownerDocument) {
        throw new UnattachedNode();
      }
      $parentNode->appendChild(
        $parentNode->ownerDocument->importNode($this->_node, TRUE)
      );
    }

    public function getIterator(): \Iterator {
      return new \ArrayIterator([$this->node]);
    }
  }
}
