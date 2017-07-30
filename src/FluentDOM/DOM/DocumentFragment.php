<?php
/**
 * FluentDOM\DOM\DocumentFragment extends PHPs DOMDocumentFragment class. It adds some namespace handling.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\DOM {

  use FluentDOM\Utility\Namespaces;

  /**
   * FluentDOM\DOM\DocumentFragment extends PHPs DOMDocumentFragment class. It adds some namespace handling and
   * some standard interfaces for convenience.
   *
   * Be aware that a fragment is empty after it was appended.
   *
   * @property-read Document $ownerDocument
   * @property-read Element $firstElementChild
   * @property-read Element $lastElementChild
   */
  class DocumentFragment
    extends \DOMDocumentFragment
    implements
      \Countable,
      \IteratorAggregate,
      Node,
      Node\ParentNode {

    use
      Node\ParentNode\Properties,
      Node\QuerySelector\Implementation,
      Node\Xpath;

    /**
     * @var Namespaces
     */
    private $_namespaces;

    /**
     * Casting the fragment to string will return the text content of all nodes
     *
     * @return string
     */
    public function __toString(): string {
      $result = '';
      foreach ($this->childNodes as $child) {
        $result .= (string)$child;
      }
      return $result;
    }

    /**
     * @return int
     */
    public function count(): int {
      return $this->childNodes->length;
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      return new \ArrayIterator(iterator_to_array($this->childNodes));
    }

    /**
     * Get/Set the namespace definition used for the fragment strings.
     *
     * You can use an [prefix => $namespaceURI, ...] or an element node
     * to set the namespaces. If the list is empty, the namespaces from
     * the document object will be used.
     *
     * @param NULL|array|\Traversable|\DOMElement $namespaces
     * @return Namespaces
     */
    public function namespaces($namespaces = NULL): Namespaces {
      if (isset($namespaces) || (!$this->_namespaces instanceof Namespaces)) {
        $this->_namespaces = new Namespaces();
      }
      if (isset($namespaces)) {
        if ($namespaces instanceof \DOMElement) {
          $xpath = new Xpath($namespaces->ownerDocument);
          $namespaces = $xpath('namespace::*', $namespaces);
        }
        if (is_array($namespaces) || $namespaces instanceof \Traversable) {
          foreach ($namespaces as $key => $namespaceURI) {
            if ($namespaceURI instanceof \DOMNameSpaceNode) {
              if ($namespaceURI->nodeName === 'xmlns') {
                $this->registerNamespace('#default', $namespaceURI->nodeValue);
              } elseif ($namespaceURI->localName != 'xml') {
                $this->registerNamespace($namespaceURI->localName, $namespaceURI->nodeValue);
              }
            } else {
              $this->registerNamespace($key, $namespaceURI);
            }
          }
        } elseif (isset($namespaces)) {
          throw new \InvalidArgumentException(
            '$namespaces needs to be a list of namespaces or an element node to fetch the namespaces from.'
          );
        }
      }
      return count($this->_namespaces) > 0 ? $this->_namespaces : $this->ownerDocument->namespaces();
    }

    /**
     * Register a namespace prefix to use it in appendXml()
     *
     * @param string $prefix
     * @param string $namespaceURI
     */
    public function registerNamespace(string $prefix, string $namespaceURI) {
      $this->namespaces()[empty($prefix) ? '#default' : $prefix] = $namespaceURI;
    }

    /**
     * Append an xml to the fragment, it can use namespace prefixes defined on the fragment object.
     *
     * @param string $data
     * @param NULL|array|\Traversable|\DOMElement $namespaces
     * @return bool
     */
    public function appendXml($data, $namespaces = NULL): bool {
      $namespaces = $this->namespaces($namespaces);
      if (count($namespaces) == 0) {
        return parent::appendXml($data);
      } else {
        $fragment = '<fragment';
        foreach ($namespaces as $key => $xmlns) {
          $prefix = $key === '#default' ? '' : $key;
          $fragment .= ' '.htmlspecialchars(empty($prefix) ? 'xmlns' : 'xmlns:'.$prefix);
          $fragment .= '="'.htmlspecialchars($xmlns).'"';
        }
        $fragment .= '>'.$data.'</fragment>';
        $source = new Document();
        if ($source->loadXML($fragment)) {
          foreach ($source->documentElement->childNodes as $child) {
            $this->appendChild($this->ownerDocument->importNode($child, TRUE));
          }
          return TRUE;
        } else {
          return FALSE;
        }
      }
    }

    /**
     * Append an child element
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return Element
     */
    public function appendElement(string $name, $content = '', array $attributes = NULL): Element {
      $this->appendChild(
        $node = $this->ownerDocument->createElement($name, $content, $attributes)
      );
      return $node;
    }


    /**
     * Save as XML string
     *
     * @return string
     */
    public function saveXmlFragment(): string {
      return $this->ownerDocument->saveXML($this);
    }
  }
}
