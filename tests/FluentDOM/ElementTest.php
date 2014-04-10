<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class ElementTest extends TestCase {

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

    /**
     * @covers FluentDOM\Element::setAttribute
     */
    public function testSetAttributeXmlAttribute() {
      $dom = new Document();
      $dom->registerNamespace('foo', 'urn:foo');
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement->setAttribute('xml:id', 'value');
      $this->assertEquals(
        '<root xml:id="value"/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::setAttribute
     */
    public function testSetAttributeXmlnsAttribute() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement->setAttribute('xmlns:foo', 'urn:foo');
      $this->assertEquals(
        '<root xmlns:foo="urn:foo"/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     */
    public function testAppend() {
      $object = $this->getMock('FluentDOM\\Appendable');
      $object
        ->expects($this->once())
        ->method('appendTo')
        ->with($this->isInstanceOf('FluentDOM\\Element'))
        ->will(
          $this->returnCallback(
            function(Element $parentNode) {
              return $parentNode->appendElement('success');
            }
          )
        );
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $node = $dom->documentElement->append($object);
      $this->assertEquals(
        '<root><success/></root>',
        $dom->saveXML($dom->documentElement)
      );
      $this->assertEquals(
        "success", $node->tagName
      );
    }

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
     * @covers FluentDOM\Element::appendXml
     */
    public function testAppendXml() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement->appendXml(
        '<test attribute="value">text</test>'
      );
      $this->assertEquals(
        '<root><test attribute="value">text</test></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::appendXml
     */
    public function testAppendXmlWithNamspace() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement->appendXml(
        '<foo:test xmlns:foo="urn:foo" foo:attribute="value">text</foo:test>'
      );
      $this->assertEquals(
        '<root><foo:test xmlns:foo="urn:foo" foo:attribute="value">text</foo:test></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::saveXml
     */
    public function testSaveXml() {
      $dom = new Document();
      $node = $dom->appendChild($dom->createElement('div'));
      $this->assertEquals('<div/>', $node->saveXml());
    }

    /**
     * @covers FluentDOM\Element::saveHtml
     */
    public function testSaveHtml() {
      $dom = new Document();
      $node = $dom->appendChild($dom->createElement('div'));
      $this->assertEquals('<div></div>', $node->saveHtml());
    }
  }
}