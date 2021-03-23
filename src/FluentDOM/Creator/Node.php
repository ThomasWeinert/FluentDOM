<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
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

    /**
     * @var Document
     */
    private $_document;

    /**
     * @var \DOMElement
     */
    private $_node;

    /**
     * @var Creator
     */
    private $_creator;

    /**
     * @param Creator $creator
     * @param Document $document
     * @param \DOMElement $node
     */
    public function __construct(Creator $creator, Document $document, \DOMElement $node) {
      $this->_creator = $creator;
      $this->_document = $document;
      $this->_node = $node;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __isset(string $name): bool {
      switch ($name) {
      case 'document' :
      case 'node' :
        return TRUE;
      }
      return FALSE;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
      switch ($name) {
      case 'document' :
        return $this->getDocument();
      case 'node' :
        return $this->_node;
      }
      return NULL;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \BadMethodCallException
     */
    public function __set(string $name, $value) {
      throw new \BadMethodCallException(
        \sprintf('%s is immutable.', \get_class($this))
      );
    }

    /**
     * @param string $name
     * @throws \BadMethodCallException
     */
    public function __unset(string $name) {
      throw new \BadMethodCallException(
        \sprintf('%s is immutable.', \get_class($this))
      );
    }

    /**
     * @return Document
     */
    public function getDocument(): Document {
      $document = clone $this->_document;
      $document->appendChild($document->importNode($this->_node, TRUE));
      if ($this->_creator->optimizeNamespaces) {
        try {
          $document = (new Optimize($document))->getDocument();
          $document->formatOutput = $this->_document->formatOutput;
        } catch (UnattachedNode $exception) {
        }
      }
      return $document;
    }

    /**
     * @return string
     */
    public function __toString(): string {
      return $this->getDocument()->saveXML() ?: '';
    }

    /**
     * @param Element $parentNode
     */
    public function appendTo(Element $parentNode): void {
      $parentNode->appendChild(
        $parentNode->ownerDocument->importNode($this->_node, TRUE)
      );
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      return new \ArrayIterator([$this->node]);
    }
  }
}
