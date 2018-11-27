<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\DOM\Document;

  require_once __DIR__.'/../TestCase.php';

  class Issue80Test extends TestCase {

    public function testRemoveAttributeNSShouldNotRemoveUsedNamespace() {
      $document = new Document();
      $document->appendChild($document->createElement('foo'));
      $document->documentElement->setAttributeNS('urn:bar', 'bar:bar', '42');
      $document->documentElement->setAttributeNS('urn:bar', 'bar:foobar', '42');
      $document->documentElement->removeAttributeNS('urn:bar', 'bar');
      $this->assertXmlStringEqualsXmlString(
        '<foo xmlns:bar="urn:bar" bar:foobar="42"/>',
        $document->saveXML()
      );
    }

    public function testRemoveAttributeNSShouldRemoveNamespaceDefinition() {
      $document = new Document();
      $document->appendChild($document->createElement('foo'));
      $document->documentElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:bar', 'urn:example');
      $document->documentElement->removeAttributeNS('http://www.w3.org/2000/xmlns/', 'bar');
      $this->assertXmlStringEqualsXmlString(
        '<foo/>',
        $document->saveXML()
      );
    }

    public function testRemoveAttributeNSShouldKeepUsedNamespaceDefinition() {
      $document = new Document();
      $document->appendChild($document->createElement('foo'));
      $document->documentElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:bar', 'urn:example');
      $document->documentElement->setAttributeNS('urn:example', 'bar:foobar', '42');
      $document->documentElement->removeAttributeNS('http://www.w3.org/2000/xmlns/', 'bar');
      $this->assertXmlStringEqualsXmlString(
        '<foo xmlns:bar="urn:example" bar:foobar="42"/>',
        $document->saveXML()
      );
    }
  }
}
