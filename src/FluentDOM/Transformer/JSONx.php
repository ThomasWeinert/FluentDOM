<?php

namespace FluentDOM\Transformer {

  use FluentDOM\Document;
  use FluentDOM\Element;
  use FluentDOM\Xpath;

  class JSONx {

    const XMLNS_JSONX = 'http://www.ibm.com/xmlns/prod/2009/jsonx';
    const XMLNS_JSONDOM = 'urn:carica-json-dom.2013';

    /**
     * @var \DOMDocument
     */
    private $_document = NULL;

    /**
     * @var string
     */
    private $_prefix = 'json:';

    /**
     * Import a DOM document and use the JsonDOM rules to convert it into JSONx.
     *
     * @param \DOMDocument $document
     */
    public function __construct(\DOMDocument $document) {
      $this->_document = $document;
    }

    /**
     * Create a JSONX document and return it as xml string
     *
     * @return string
     */
    public function __toString() {
      return $this->getDocument()->saveXml();
    }

    /**
     * Create and return a JSONx document.
     *
     * @return Document
     */
    public function getDocument() {
      $document = new Document();
      $document->registerNamespace('json', self::XMLNS_JSONX);
      $this->addNode($document, $this->_document->documentElement);
      return $document;
    }

    /**
     * @param Document|Element $parent
     * @param \DOMNode $node
     * @param bool $addNameAttribute
     */
    public function addNode($parent, $node, $addNameAttribute = FALSE) {
      $xpath = new Xpath($node->ownerDocument);
      if ($node->hasAttributeNS(self::XMLNS_JSONDOM, 'type')) {
        $type = $node->getAttributeNS(self::XMLNS_JSONDOM, 'type');
      } else {
        $type = $xpath('count(*) > 0', $node) ? 'object' : 'string';
      }
      switch ($type) {
      case 'object' :
        $result = $parent->appendElement('json:object');
        /** @var \DOMElement $child */
        foreach ($xpath('*', $node) as $child) {
          $this->addNode($result, $child, TRUE);
        }
        break;
      case 'array' :
        $result = $parent->appendElement('json:array');
        foreach ($xpath('*', $node) as $child) {
          $this->addNode($result, $child, FALSE);
        }
        break;
      case 'number' :
        $result = $parent->appendElement('json:number', $node->nodeValue);
        break;
      case 'boolean' :
        $result = $parent->appendElement('json:boolean', $node->nodeValue);
        break;
      case 'null' :
        $result = $parent->appendElement('json:null');
        break;
      default :
        $result = $parent->appendElement('json:string', $node->nodeValue);
        break;
      }
      if ($addNameAttribute) {
        $name = $node->localName;
        if ($node->hasAttributeNS(self::XMLNS_JSONDOM, 'name')) {
          $name = $node->getAttributeNS(self::XMLNS_JSONDOM, 'name');
        }
        $result['name'] = $name;
      }
    }
  }
}