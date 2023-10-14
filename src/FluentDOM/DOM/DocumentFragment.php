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
      Node\ParentNode {

    use
      Node\ParentNode\Implementation,
      /** @noinspection TraitsPropertiesConflictsInspection */
      Node\QuerySelector\Implementation,
      /** @noinspection TraitsPropertiesConflictsInspection */
      Node\Xpath;

    private ?Namespaces $_namespaces = NULL;

    /**
     * Casting the fragment to string will return the text content of all nodes
     */
    public function __toString(): string {
      return implode('', iterator_to_array($this->childNodes));
    }

    public function count(): int {
      return $this->childNodes->length;
    }

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
     * @throws \InvalidArgumentException
     */
    public function namespaces(iterable|\DOMElement $namespaces = NULL): Namespaces {
      if (NULL !== $namespaces || (!$this->_namespaces instanceof Namespaces)) {
        $this->_namespaces = new Namespaces();
      }
      if (NULL !== $namespaces) {
        if ($namespaces instanceof \DOMElement) {
          $xpath = new Xpath($namespaces->ownerDocument);
          /** @noinspection CallableParameterUseCaseInTypeContextInspection */
          $namespaces = $xpath('namespace::*', $namespaces);
        }
        if (is_iterable($namespaces)) {
          foreach ($namespaces as $key => $namespaceURI) {
            if ($namespaceURI instanceof \DOMNameSpaceNode) {
              if ($namespaceURI->nodeName === 'xmlns') {
                $this->registerNamespace('#default', $namespaceURI->nodeValue);
              } elseif ($namespaceURI->localName !== 'xml') {
                $this->registerNamespace($namespaceURI->localName, $namespaceURI->nodeValue);
              }
            } else {
              $this->registerNamespace($key, $namespaceURI);
            }
          }
        }
      }
      return \count($this->_namespaces) > 0 ? $this->_namespaces : $this->ownerDocument->namespaces();
    }

    /**
     * Register a namespace prefix to use it in appendXml()
     *
     * @param string $prefix
     * @param string $namespaceURI
     * @throws \InvalidArgumentException
     */
    public function registerNamespace(string $prefix, string $namespaceURI): void {
      $this->namespaces()[empty($prefix) ? '#default' : $prefix] = $namespaceURI;
    }

    /**
     * Append an xml to the fragment, it can use namespace prefixes defined on the fragment object.
     *
     * @param string $data
     * @param NULL|iterable|\DOMElement $namespaces
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function appendXml(string $data, iterable|\DOMElement $namespaces = NULL): bool {
      $namespaces = $this->namespaces($namespaces);
      if (\count($namespaces) === 0) {
        return parent::appendXML($data);
      }
      $fragment = '<fragment';
      foreach ($namespaces as $key => $xmlns) {
        $prefix = $key === '#default' ? '' : $key;
        $fragment .= ' '.\htmlspecialchars(empty($prefix) ? 'xmlns' : 'xmlns:'.$prefix);
        $fragment .= '="'.\htmlspecialchars($xmlns).'"';
      }
      $fragment .= '>'.$data.'</fragment>';
      $source = new Document();
      if ($source->loadXML($fragment)) {
        foreach ($source->documentElement->childNodes as $child) {
          $this->appendChild($this->ownerDocument->importNode($child, TRUE));
        }
        return TRUE;
      }
      return FALSE;
    }

    /**
     * Append an child element
     *
     * @throws \LogicException|\DOMException
     */
    public function appendElement(
      string $name, string|array $content = '', array $attributes = NULL
    ): Element {
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
