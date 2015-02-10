<?php
/**
 * A loader that converts JSONx into JsonDOM
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\QualifiedName;

  /**
   * A lazy load group for php class loaders
   *
   * This defines loaders for PHP classes like SimpleXML
   */
  class JSONx implements Loadable {

    use Supports;

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
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if ($this->supports($contentType) && !empty($source)) {
        $dom = new Document();
        $dom->preserveWhiteSpace = FALSE;
        $dom->registerNamespace('jx', self::XMLNS_JSONX);
        if ($this->startsWith($source, '<')) {
          $dom->loadXml($source);
        } else {
          $dom->load($source);
        }
        $target = new Document();
        $target->registerNamespace('json', self::XMLNS_JSONDOM);
        if (isset($dom->documentElement)) {
          $this->transferNode($dom->documentElement, $target);
        }
        return $target;
      }
      return NULL;
    }

    /**
     * @param Element $node
     * @param \DOMNode|Document|Element $target
     */
    private function transferNode(Element $node, \DOMNode $target) {
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