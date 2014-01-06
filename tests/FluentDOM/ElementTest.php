<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class ElementTest extends TestCase {

    /**
     * @covers FluentDOM\Element::appendElement
     */
    public function testAppendElement() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement->appendElement('test', 'text', array('attribute' => 'value'));
      $this->assertEquals(
        '<root><test attribute="value">text</test></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::setAttribute
     */
    public function testSetAttribute() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement->setAttribute('attribute', 'value');
      $this->assertEquals(
        '<root attribute="value"/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::setAttribute
     */
    public function testSetAttributeWithNamespace() {
      $dom = new Document();
      $dom->registerNamespace('foo', 'urn:foo');
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement->setAttribute('foo:attribute', 'value');
      $this->assertEquals(
        '<root xmlns:foo="urn:foo" foo:attribute="value"/>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}