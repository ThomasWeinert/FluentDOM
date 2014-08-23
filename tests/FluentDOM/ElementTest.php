<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class ElementTest extends TestCase {

    /**
     * @covers FluentDOM\Element::__toString
     */
    public function testMagicMethodToString() {
      $dom = new Document();
      $dom->appendElement('test', 'success');
      $this->assertEquals(
        'success',
        (string)$dom->documentElement
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
     * @covers FluentDOM\Element::resolveTagName
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
     * @covers FluentDOM\Element::resolveTagName
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
     * @covers FluentDOM\Element::resolveTagName
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
     * @covers FluentDOM\Element::resolveTagName
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
     * @covers FluentDOM\Element::applyNamespaces
     * @covers FluentDOM\Element::isCurrentNamespace
     * @covers FluentDOM\Element::getDocument
     */
    public function testApplyNamespaces() {
      $dom = new Document();
      $dom->registerNamespace('#default', 'urn:default');
      $dom->registerNamespace('foo', 'urn:foo');
      $node = $dom->appendElement('bar');
      $node->applyNamespaces();
      $this->assertEquals(
        '<bar xmlns="urn:default" xmlns:foo="urn:foo"/>', $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Element::applyNamespaces
     * @covers FluentDOM\Element::isCurrentNamespace
     * @covers FluentDOM\Element::getDocument
     */
    public function testApplyNamespacesWithTwoNamespaces() {
      $dom = new Document();
      $dom->registerNamespace('foo', 'urn:foo');
      $dom->registerNamespace('bar', 'urn:bar');
      $node = $dom->appendElement('bar');
      $node->applyNamespaces();
      $this->assertEquals(
        '<bar xmlns:foo="urn:foo" xmlns:bar="urn:bar"/>', $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Element::applyNamespaces
     * @covers FluentDOM\Element::isCurrentNamespace
     * @covers FluentDOM\Element::getDocument
     */
    public function testApplyNamespacesWithOneOfTwoNamespaces() {
      $dom = new Document();
      $dom->registerNamespace('foo', 'urn:foo');
      $dom->registerNamespace('bar', 'urn:bar');
      $node = $dom->appendElement('bar');
      $node->applyNamespaces('bar');
      $this->assertEquals(
        '<bar xmlns:bar="urn:bar"/>', $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Element::applyNamespaces
     * @covers FluentDOM\Element::isCurrentNamespace
     * @covers FluentDOM\Element::getDocument
     */
    public function testApplyNamespacesWithTwoOfThreeNamespaces() {
      $dom = new Document();
      $dom->registerNamespace('foo', 'urn:foo');
      $dom->registerNamespace('bar', 'urn:bar');
      $dom->registerNamespace('foobar', 'urn:foobar');
      $node = $dom->appendElement('bar');
      $node->applyNamespaces(['foo', 'bar']);
      $this->assertEquals(
        '<bar xmlns:foo="urn:foo" xmlns:bar="urn:bar"/>', $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
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
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithText() {
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append('success');
      $this->assertEquals(
        '<root>success</root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithArraySetsAttributes() {
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(['result' => 'success']);
      $this->assertEquals(
        '<root result="success"/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithNode() {
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(
        $dom->createElement('success')
      );
      $this->assertEquals(
        '<root><success/></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithNodeAppendsClone() {
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(
        $dom->documentElement
      );
      $this->assertEquals(
        '<root><root/></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithAttributeNode() {
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(
         $dom->createAttribute('result', 'success')
      );
      $this->assertEquals(
        '<root result="success"/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithDocument() {
      $import = new \DOMDocument();
      $import->loadXml('<success/>');
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(
        $import
      );
      $this->assertEquals(
        '<root><success/></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithEmptyDocument() {
      $import = new \DOMDocument();
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(
        $import
      );
      $this->assertEquals(
        '<root/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithNodeFromOtherDocument() {
      $import = new \DOMDocument();
      $import->loadXml('<success/>');
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(
        $import->documentElement
      );
      $this->assertEquals(
        '<root><success/></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithNodeListFromOtherDocument() {
      $import = new Document();
      $import->loadXml('<success/>');
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(
        $import->evaluate('/*')
      );
      $this->assertEquals(
        '<root><success/></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithArrayContainingNodes() {
      $dom = new Document();
      $dom
        ->appendElement('root')
        ->append(
          [
            'attr' => 42,
            $dom->createComment('success'),
            $dom->createCDATASection('success')
          ]
        );
      $this->assertEquals(
        '<root attr="42"><!--success--><![CDATA[success]]></root>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithAttributeNodeFromOtherDocument() {
      $import = new \DOMDocument();
      $import->loadXml('<root result="success"/>');
      $dom = new Document();
      $dom->appendElement('root');
      $dom->documentElement->append(
        $import->documentElement->getAttributeNode('result')
      );
      $this->assertEquals(
        '<root result="success"/>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element::append
     * @covers FluentDOM\Element::appendNode
     */
    public function testAppendWithClosure() {
      $dom = new Document();
      $dom
        ->appendElement('root')
        ->append(
          function () use ($dom) {
            return $dom->createCDATASection('success');
          }
        );
      $this->assertEquals(
        '<root><![CDATA[success]]></root>',
        $dom->saveXML($dom->documentElement)
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
     * @covers FluentDOM\Element
     * @covers FluentDOM\Node\Xpath
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
     * @covers FluentDOM\Node\Xpath
     */
    public function testMagicMethodInvoke() {
      $dom = new Document();
      $dom->loadXml('<foo>success</foo>');
      $node = $dom->documentElement;
      $this->assertEquals(
        'success',
        $node('string(.)')
      );
    }

    /**
     * @cover FluentDOM\Element:find
     */
    public function testFind() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $fd = $dom->documentElement->find('bar');
      $this->assertInstanceOf('FluentDOM\Query', $fd);
      $this->assertSame(
        $dom->documentElement->firstChild,
        $fd[0]
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
    public function testArrayAccessOffsetExistsExpectingException() {
      $dom = new Document();
      $dom->loadXML(self::XML);
      $this->setExpectedException(
        'InvalidArgumentException',
        'Invalid offset. Use integer for child nodes and strings for attributes.'
      );
      $dom->documentElement[NULL];
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
    public function testArrayAccessOffsetSetWithInvalidChildExpectingException() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $this->setExpectedException(
        'InvalidArgumentException',
        '$value is not a valid \DOMNode'
      );
      $dom->documentElement[0] = NULL;
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

    /**
     * @covers FluentDOM\Element::getIterator
     */
    public function testGetIterator() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/><bar/></foo>');
      $this->assertEquals(
        array(
          $dom->documentElement->firstChild,
          $dom->documentElement->firstChild
        ),
        iterator_to_array(
          $dom->documentElement
        )
      );
    }

    /**
     * @covers FluentDOM\Element::count
     */
    public function testCountable() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $this->assertCount(
        1, $dom->documentElement
      );
    }
  }
}