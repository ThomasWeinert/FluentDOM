<?php
/**
 * A loader that converts JSONx into JsonDOM
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\QualifiedName;

  /**
   * A lazy load group for php class loaders
   *
   * This defines loaders for PHP classes like SimpleXML
   */
  class JSONx implements Loadable {

    use Supports\Libxml;

    const XMLNS_JSONX = 'http://www.ibm.com/xmlns/prod/2009/jsonx';
    const XMLNS_JSONDOM = 'urn:carica-json-dom.2013';
    const DEFAULT_QNAME = '_';

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('jsonx', 'application/xml+jsonx');
    }

    /**
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, $options = []) {
      if ($this->supports($contentType) && !empty($source)) {
        $document = $this->loadXmlDocument($source, $contentType, $options);
        $target = new Document();
        $target->registerNamespace('json', self::XMLNS_JSONDOM);
        if (isset($document->documentElement)) {
          $this->transferNode($document->documentElement, $target);
        }
        return $target;
      }
      return NULL;
    }

     /**
      * @see Loadable::loadFragment
      *
      * @param string $source
      * @param string $contentType
      * @param array|\Traversable|Options $options
      * @return DocumentFragment|NULL
      */
    public function loadFragment($source, $contentType, $options = []) {
      if ($this->supports($contentType) && !empty($source)) {
        $document = new Document();
        $document->preserveWhiteSpace = FALSE;
        $document->registerNamespace('jx', self::XMLNS_JSONX);
        $sourceFragment = $document->createDocumentFragment();
        $sourceFragment->appendXml($source);
        $target = new Document();
        $target->registerNamespace('json', self::XMLNS_JSONDOM);
        $targetFragment = $target->createDocumentFragment();
        foreach ($sourceFragment->childNodes as $node) {
          $this->transferNode($node, $targetFragment);
        }
        return $targetFragment;
      }
      return NULL;
    }

    /**
     * @param \DOMNode|Element $node
     * @param \DOMNode|Document|Element $target
     */
    private function transferNode(\DOMNode $node, \DOMNode $target) {
      if ($node->namespaceURI === self::XMLNS_JSONX) {
        if ($target instanceOf Document) {
          $normalizedName = $name = 'json:json';
        } else {
          $name = $node->getAttribute('name');
          $normalizedName = QualifiedName::normalizeString($name, self::DEFAULT_QNAME);
        }
        $type = $node->localName;
        $newNode = $target->appendElement($normalizedName);
        if ($name !== $normalizedName && $name !== '') {
          $newNode->setAttribute('json:name', $name);
        }
        switch ($type) {
        case 'object' :
          if ($node('count(*) > 0')) {
            foreach ($node('*') as $childNode) {
              $this->transferNode($childNode, $newNode);
            }
            return;
          }
          break;
        case 'array' :
          foreach ($node('*') as $childNode) {
            $this->transferNode($childNode, $newNode);
          }
          break;
        case 'string' :
          $newNode->append((string)$node);
          return;
        default :
          $newNode->append((string)$node);
          break;
        }
        $newNode->setAttribute('json:type', $type);
      }
    }
  }
}