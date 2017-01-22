<?php
/**
 * FluentDOM\DocumentFragment extends PHPs DOMDocumentFragment class. It adds some namespace handling.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\DocumentFrag,ent extends PHPs DOMDocumentFragment class. It adds some namespace handling and
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
    public function __toString() {
      $result = '';
      foreach ($this->childNodes as $child) {
        $result .= (string)$child;
      }
      return $result;
    }

    /**
     * @return int
     */
    public function count() {
      return $this->childNodes->length;
    }

    /**
     * @return \Iterator
     */
    public function getIterator() {
      return new \ArrayIterator(iterator_to_array($this->childNodes));
    }

    /**
     * Get/Set the namespace definition used for the fragment strings.
     *
     * You can use an array(prefix => $namespace, ...) or an element node
     * to set the namespaces. If the list is empty, the namespaces from
     * the document object will be used.
     *
     * @param null|array|\Traversable|\DOMElement $namespaces
     * @return Namespaces
     */
    public function namespaces($namespaces = NULL) {
      if (isset($namespaces) || (!$this->_namespaces instanceof Namespaces)) {
        $this->_namespaces = new Namespaces();
      }
      if (isset($namespaces)) {
        if ($namespaces instanceof \DOMElement) {
          $xpath = new Xpath($namespaces->ownerDocument);
          $namespaces = $xpath('namespace::*', $namespaces);
        }
        if (is_array($namespaces) || $namespaces instanceof \Traversable) {
          foreach ($namespaces as $key => $namespace) {
            if ($namespace instanceof \DOMNameSpaceNode) {
              if ($namespace->nodeName === 'xmlns') {
                $this->registerNamespace('#default', $namespace->nodeValue);
              } elseif ($namespace->localName != 'xml') {
                $this->registerNamespace($namespace->localName, $namespace->nodeValue);
              }
            } else {
              $this->registerNamespace($key, $namespace);
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
     * @param $prefix
     * @param $namespace
     */
    public function registerNamespace($prefix, $namespace) {
      $this->namespaces()[empty($prefix) ? '#default' : $prefix] = $namespace;
    }

    /**
     * Append an xml to the fragment, it can use namespace prefixes defined on the fragment object.
     *
     * @param string $data
     * @param null|array|\Traversable|\DOMElement $namespaces
     * @return bool
     */
    public function appendXml($data, $namespaces = NULL) {
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
    public function appendElement($name, $content = '', array $attributes = NULL) {
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
    public function saveXmlFragment() {
      return $this->ownerDocument->saveXML($this);
    }
  }
}

