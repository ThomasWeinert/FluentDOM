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
     * @covers FluentDOM\Element::hasAttribute
     */
    public function testHasAttributeExpectingTrue() {
      $dom = new Document();
      $dom->loadXml('<root attribute="value"/>');
      $this->assertTrue(
        $dom->documentElement->hasAttribute('attribute')
      );
    }

    /**
     * @covers FluentDOM\Element::hasAttribute
     */
    public function testHasAttributeExpectingFalse() {
      $dom = new Document();
      $dom->loadXml('<root/>');
      $this->assertFalse(
        $dom->documentElement->hasAttribute('attribute')
      );
    }

    /**
     * @covers FluentDOM\Element::hasAttribute
     */
    public function testHasAttributeWithNamespaceExpectingTrue() {
      $dom = new Document();
      $dom->loadXml('<root xmlns:foo="urn:foo" foo:attribute="value"/>');
      $dom->registerNamespace('foo', 'urn:foo');
      $this->assertTrue(
        $dom->documentElement->hasAttribute('foo:attribute')
      );
    }

    /**
     * @covers FluentDOM\Element::hasAttribute
     */
    public function testHasAttributeWithNamespaceExpectingFalse() {
      $dom = new Document();
      $dom->loadXml('<root xmlns:foo="urn:foo" foo:attribute="value"/>');
      $dom->registerNamespace('foo', 'urn:BAR');
      $this->assertFalse(
        $dom->documentElement->hasAttribute('foo:attribute')
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
    public function testAppendXmlWithNamespace() {
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
     * @covers FluentDOM\Element::saveXmlFragment
     */
    public function testSaveXmlFragment() {
      $dom = new Document();
      $node = $dom->appendChild($dom->createElement('div'));
      $node->appendChild($dom->createTextNode("text"));
      $node->appendChild($dom->createElement("br"));
      $this->assertEquals('text<br/>', $node->saveXmlFragment());
    }

    /**
     * @covers FluentDOM\Element::saveHtml
     */
    public function testSaveHtml() {
      $dom = new Document();
      $node = $dom->appendChild($dom->createElement('div'));
      $this->assertEquals('<div></div>', $node->saveHtml());
    }

    /**
     * @covers FluentDOM\Element::evaluate
     */
    public function testEvaluate() {
      $dom = new Document();
      $dom->loadXml('<foo>success</foo>');
      $this->assertEquals(
        'success',
        $dom->documentElement->evaluate('string(.)')
      );
    }

    /**
     * @covers FluentDOM\Element
     * @dataProvider provideExistingOffsets
     */
    public function testArrayAccessOffsetExistsExpectingTrue($offset) {
      $dom = new Document();
      $dom->loadXML(self::XML);
      $this->assertTrue(isset($dom->documentElement[$offset]));
    }

    public static function provideExistingOffsets() {
      return array(
        array(0),
        array(1),
        array('version')
      );
    }

    /**
     * @covers FluentDOM\Element
     * @dataProvider provideMissingOffsets
     */
    public function testArrayAccessOffsetExistsExpectingFalse($offset) {
      $dom = new Document();
      $dom->loadXML(self::XML);
      $this->assertFalse(isset($dom->documentElement[$offset]));
    }

    public static function provideMissingOffsets() {
      return array(
        array(99),
        array('NON_EXISTING')
      );
    }

    /**
     * @covers FluentDOM\Element
     */
    public function testArrayAccessOffsetGetWithItem() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/><foobar/></foo>');
      $this->assertEquals(
        '<foobar/>',
        $dom->saveXML($dom->documentElement[1])
      );
    }

    /**
     * @covers FluentDOM\Element
     */
    public function testArrayAccessOffsetGetWithAttribute() {
      $dom = new Document();
      $dom->loadXML('<foo attribute="success"/>');
      $this->assertEquals(
        'success',
        $dom->documentElement['attribute']
      );
    }

    /**
     * @covers FluentDOM\Element
     */
    public function testArrayAccessOffsetGetWithChaining() {
      $dom = new Document();
      $dom->loadXML('<foo><bar attribute="success"/></foo>');
      $this->assertEquals(
        'success',
        $dom->documentElement[0]['attribute']
      );
    }

    /**
     * @covers FluentDOM\Element
     */
    public function testArrayAccessOffsetSetAppendChild() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement[] = $dom->createElement('success');
      $this->assertEquals(
        '<root><success/></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element
     */
    public function testArrayAccessOffsetSetReplaceChild() {
      $dom = new Document();
      $dom
        ->appendChild($dom->createElement('root'))
        ->appendChild($dom->createElement('fail'));
      $dom->documentElement[0] = $dom->createElement('success');
      $this->assertEquals(
        '<root><success/></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element
     */
    public function testArrayAccessOffsetSetWriteAttribute() {
      $dom = new Document();
      $dom
        ->appendChild($dom->createElement('root'));
      $dom->documentElement['result'] = 'success';
      $this->assertEquals(
        '<root result="success"/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element
     */
    public function testArrayAccessOffsetUnsetRemoveChild() {
      $dom = new Document();
      $dom->loadXML('<root><fail/></root>');
      unset($dom->documentElement[0]);
      $this->assertEquals(
        '<root/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element
     */
    public function testArrayAccessOffsetUnsetRemoveAttribute() {
      $dom = new Document();
      $dom->loadXML('<root result="fail"/>');
      unset($dom->documentElement['result']);
      $this->assertEquals(
        '<root/>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}