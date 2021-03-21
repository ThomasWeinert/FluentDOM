<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM {

  require_once __DIR__ . '/../TestCase.php';

  use FluentDOM\Appendable;
  use FluentDOM\TestCase;

  class ElementTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Element::__toString
     */
    public function testMagicMethodToString(): void {
      $document = new Document();
      $document->appendElement('test', 'success');
      $this->assertEquals(
        'success',
        (string)$document->documentElement
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::__get
     */
    public function testGetPropertyFirstElementChild(): void {
      $document = new Document();
      $document->loadXml('<foo>TEXT<bar attr="value"/></foo>');
      $this->assertEquals(
        '<bar attr="value"/>',
        $document->documentElement->firstElementChild->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyFirstElementChildExpectingTrue(): void {
      $document = new Document();
      $document->loadXml('<foo>TEXT<bar attr="value"/></foo>');
      $this->assertTrue(isset($document->documentElement->firstElementChild));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyFirstElementChildExpectingFalse(): void {
      $document = new Document();
      $document->loadXml('<foo>TEXT</foo>');
      $this->assertFalse(isset($document->documentElement->firstElementChild));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__get
     */
    public function testGetPropertyLastElementChild(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->assertEquals(
        '<bar attr="value"/>',
        $document->documentElement->lastElementChild->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyLastElementChildExpectingTrue(): void {
      $document = new Document();
      $document->loadXml('<foo>TEXT<bar attr="value"/></foo>');
      $this->assertTrue(isset($document->documentElement->lastElementChild));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyLastElementChildExpectingFalse(): void {
      $document = new Document();
      $document->loadXml('<foo>TEXT</foo>');
      $this->assertFalse(isset($document->documentElement->lastElementChild));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__get
     */
    public function testGetPropertyNextElementSibling(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->assertEquals(
        '<bar attr="value"/>',
        $document->documentElement->firstChild->nextElementSibling->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyNextElementSiblingExpectingTrue(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->assertTrue(isset($document->documentElement->firstChild->nextElementSibling));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyNextElementSiblingExpectingFalse(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->assertFalse(isset($document->documentElement->lastChild->nextElementSibling));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__get
     */
    public function testGetPropertyPreviousElementSibling(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->assertEquals(
        '<foo/>',
        $document->documentElement->lastChild->previousElementSibling->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyPreviousElementSiblingExpectingTrue(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->assertTrue(isset($document->documentElement->lastChild->previousElementSibling));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyPreviousElementSiblingExpectingFalse(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->assertFalse(isset($document->documentElement->firstChild->previousElementSibling));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetPropertyChildElementCountExpectingTrue(): void {
      $document = new Document();
      $document->loadXml('<foo/>');
      $this->assertTrue(isset($document->documentElement->childElementCount));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__get
     */
    public function testGetInvalidProperty(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->expectPropertyIsUndefined();
      $this->assertNull($document->documentElement->INVALID_PROPERTY);
    }

    /**
     * @covers \FluentDOM\DOM\Element::__isset
     */
    public function testIssetInvalidProperty(): void {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->assertFalse(isset($document->documentElement->INVALID_PROPERTY));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__get
     * @covers \FluentDOM\DOM\Element::__set
     */
    public function testGetUnknownPropertyAfterSet(): void {
      $document = new Document();
      $node = $document->appendChild($document->createElement('foo'));
      $node->SOME_PROPERTY = 'success';
      $this->assertEquals(
        'success', $node->SOME_PROPERTY
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::__unset
     * @covers \FluentDOM\DOM\Element::blockReadOnlyProperties
     */
    public function testUnsetUnknownPropertyAfterSet(): void {
      $document = new Document();
      $node = $document->appendChild($document->createElement('foo'));
      $node->SOME_PROPERTY = 'success';
      unset($node->SOME_PROPERTY);
      $this->assertFalse(isset($node->SOME_PROPERTY));
    }

    /**
     * @covers \FluentDOM\DOM\Element::__set
     * @covers \FluentDOM\DOM\Element::blockReadOnlyProperties
     * @dataProvider dataProviderDynamicElementProperties
     * @param string $propertyName
     */
    public function testSetDynamicPropertyExpectingException(string $propertyName) {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->expectErrorMessage('Cannot write property');
      $document->documentElement->$propertyName = $document->createElement('test');
    }

    /**
     * @covers \FluentDOM\DOM\Element::__unset
     * @covers \FluentDOM\DOM\Element::blockReadOnlyProperties
     * @dataProvider dataProviderDynamicElementProperties
     * @param string $propertyName
     */
    public function testUnsetDynamicPropertyExpectingException(string $propertyName) {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      $this->expectException(\BadMethodCallException::class);
      unset($document->documentElement->$propertyName);
    }

    public static function dataProviderDynamicElementProperties() {
      return [
        ['firstElementChild'],
        ['lastElementChild'],
        ['nextElementSibling'],
        ['previousElementSibling']
      ];
    }

    /**
     * @covers \FluentDOM\DOM\Element::__unset
     * @covers \FluentDOM\DOM\Element::blockReadOnlyProperties
     * @dataProvider dataProviderDynamicElementProperties
     * @param string $propertyName
     */
    public function testUnsetUnknownProperty(string $propertyName) {
      $document = new Document();
      $document->loadXml('<foo><foo/>TEXT<bar attr="value"/></foo>');
      unset($document->documentElement->SOME_PROPERTY);
      $this->expectPropertyIsUndefined();
      $document->documentElement->SOME_PROPERTY;
    }

    /**
     * @covers \FluentDOM\DOM\Element::getAttribute
     */
    public function testGetAttribute(): void {
      $document = new Document();
      $document->loadXml('<foo attr="value"/>');
      $this->assertEquals(
        'value',
        $document->documentElement->getAttribute('attr')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::getAttribute
     */
    public function testGetAttributeWithNamespace(): void {
      $document = new Document();
      $document->loadXml('<foo xmlns:foo="urn:foo" foo:attr="value"/>');
      $document->registerNamespace('f', 'urn:foo');
      $this->assertEquals(
        'value',
        $document->documentElement->getAttribute('f:attr')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::getAttributeNode
     */
    public function testGetAttributeNode(): void {
      $document = new Document();
      $document->loadXml('<foo attr="value"/>');
      $this->assertEquals(
        'value',
        $document->documentElement->getAttributeNode('attr')->value
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::getAttributeNode
     */
    public function testGetAttributeNodeWithNamespace(): void {
      $document = new Document();
      $document->loadXml('<foo xmlns:foo="urn:foo" foo:attr="value"/>');
      $document->registerNamespace('f', 'urn:foo');
      $this->assertEquals(
        'value',
        $document->documentElement->getAttributeNode('f:attr')->value
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::setAttribute
     */
    public function testSetAttribute(): void {
      $document = new Document();
      $document->appendChild($document->createElement('root'));
      $document->documentElement->setAttribute('attribute', 'value');
      $this->assertXmlStringEqualsXmlString(
        '<root attribute="value"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::setAttribute
     */
    public function testSetAttributeWithNamespace(): void {
      $document = new Document();
      $document->registerNamespace('foo', 'urn:foo');
      $document->appendChild($document->createElement('root'));
      $document->documentElement->setAttribute('foo:attribute', 'value');
      $this->assertXmlStringEqualsXmlString(
        '<root xmlns:foo="urn:foo" foo:attribute="value"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::setIdAttribute
     */
    public function testSetIdAttribute(): void {
      $document = new Document();
      $document->loadXML('<root attribute="value"/>');
      $document->documentElement->setIdAttribute('attribute', TRUE);
      $this->assertEquals(
        $document->documentElement,
        $document->getElementById('value')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::setIdAttribute
     */
    public function testSetIdAttributeWithNamespace(): void {
      $document = new Document();
      $document->loadXML('<root xmlns:foo="urn:foo" foo:attribute="value"/>');
      $document->registerNamespace('foo', 'urn:foo');
      $document->documentElement->setIdAttribute('foo:attribute', TRUE);
      $this->assertEquals(
        $document->documentElement,
        $document->getElementById('value')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::setAttribute
     */
    public function testSetAttributeXmlAttribute(): void {
      $document = new Document();
      $document->registerNamespace('foo', 'urn:foo');
      $document->appendChild($document->createElement('root'));
      $document->documentElement->setAttribute('xml:id', 'value');
      $this->assertXmlStringEqualsXmlString(
        '<root xml:id="value"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::setAttribute
     */
    public function testSetAttributeXmlnsAttribute(): void {
      $document = new Document();
      $document->appendChild($document->createElement('root'));
      $document->documentElement->setAttribute('xmlns:foo', 'urn:foo');
      $this->assertXmlStringEqualsXmlString(
        '<root xmlns:foo="urn:foo"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::removeAttribute
     */
    public function testRemoveAttribute(): void {
      $document = new Document();
      $document->loadXML('<root attribute="value"/>');
      $document->documentElement->removeAttribute('attribute');
      $this->assertXmlStringEqualsXmlString(
        '<root/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::removeAttribute
     */
    public function testRemoveAttributeWithNamespace(): void {
      $document = new Document();
      $document->loadXML('<root xmlns:foo="urn:foo" foo:attribute="value"/>');
      $document->registerNamespace('foo', 'urn:foo');
      $document->documentElement->removeAttribute('foo:attribute');
      $this->assertXmlStringEqualsXmlString(
        '<root xmlns:foo="urn:foo"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::removeAttributeNS
     */
    public function testRemoveAttributeNS(): void {
      $document = new Document();
      $document->loadXML('<root xmlns:foo="urn:foo" foo:attribute="value"/>');
      $document->documentElement->removeAttributeNS('urn:foo', 'attribute');
      $this->assertXmlStringEqualsXmlString(
        '<root xmlns:foo="urn:foo"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::hasAttribute
     * @covers \FluentDOM\DOM\Element::resolveTagName
     */
    public function testHasAttributeExpectingTrue(): void {
      $document = new Document();
      $document->loadXml('<root attribute="value"/>');
      $this->assertTrue(
        $document->documentElement->hasAttribute('attribute')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::hasAttribute
     * @covers \FluentDOM\DOM\Element::resolveTagName
     */
    public function testHasAttributeExpectingFalse(): void {
      $document = new Document();
      $document->loadXml('<root/>');
      $this->assertFalse(
        $document->documentElement->hasAttribute('attribute')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::hasAttribute
     * @covers \FluentDOM\DOM\Element::resolveTagName
     */
    public function testHasAttributeWithNamespaceExpectingTrue(): void {
      $document = new Document();
      $document->loadXml('<root xmlns:foo="urn:foo" foo:attribute="value"/>');
      $document->registerNamespace('foo', 'urn:foo');
      $this->assertTrue(
        $document->documentElement->hasAttribute('foo:attribute')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::hasAttribute
     * @covers \FluentDOM\DOM\Element::resolveTagName
     */
    public function testHasAttributeWithNamespaceExpectingFalse(): void {
      $document = new Document();
      $document->loadXml('<root xmlns:foo="urn:foo" foo:attribute="value"/>');
      $document->registerNamespace('foo', 'urn:BAR');
      $this->assertFalse(
        $document->documentElement->hasAttribute('foo:attribute')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::applyNamespaces
     * @covers \FluentDOM\DOM\Element::isCurrentNamespace
     * @covers \FluentDOM\DOM\Element::getDocument
     */
    public function testApplyNamespaces(): void {
      $document = new Document();
      $document->registerNamespace('#default', 'urn:default');
      $document->registerNamespace('foo', 'urn:foo');
      $node = $document->appendElement('bar');
      $node->applyNamespaces();
      $this->assertXmlStringEqualsXmlString(
        '<bar xmlns="urn:default" xmlns:foo="urn:foo"/>', $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::applyNamespaces
     * @covers \FluentDOM\DOM\Element::isCurrentNamespace
     * @covers \FluentDOM\DOM\Element::getDocument
     */
    public function testApplyNamespacesWithTwoNamespaces(): void {
      $document = new Document();
      $document->registerNamespace('foo', 'urn:foo');
      $document->registerNamespace('bar', 'urn:bar');
      $node = $document->appendElement('bar');
      $node->applyNamespaces();
      $this->assertXmlStringEqualsXmlString(
        '<bar xmlns:foo="urn:foo" xmlns:bar="urn:bar"/>', $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::applyNamespaces
     * @covers \FluentDOM\DOM\Element::isCurrentNamespace
     * @covers \FluentDOM\DOM\Element::getDocument
     */
    public function testApplyNamespacesWithOneOfTwoNamespaces(): void {
      $document = new Document();
      $document->registerNamespace('foo', 'urn:foo');
      $document->registerNamespace('bar', 'urn:bar');
      $node = $document->appendElement('bar');
      $node->applyNamespaces('bar');
      $this->assertXmlStringEqualsXmlString(
        '<bar xmlns:bar="urn:bar"/>', $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::applyNamespaces
     * @covers \FluentDOM\DOM\Element::isCurrentNamespace
     * @covers \FluentDOM\DOM\Element::getDocument
     */
    public function testApplyNamespacesWithTwoOfThreeNamespaces(): void {
      $document = new Document();
      $document->registerNamespace('foo', 'urn:foo');
      $document->registerNamespace('bar', 'urn:bar');
      $document->registerNamespace('foobar', 'urn:foobar');
      $node = $document->appendElement('bar');
      $node->applyNamespaces(['foo', 'bar']);
      $this->assertXmlStringEqualsXmlString(
        '<bar xmlns:foo="urn:foo" xmlns:bar="urn:bar"/>', $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testAppendChildReturnsAppendedNode(): void {
      $document = new Document();
      $appended = $document->appendChild($created = $document->createElement('foo'));
      $this->assertSame($appended, $created);
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppend(): void {
      $object = $this->getMockBuilder(Appendable::class)->getMock();
      $object
        ->expects($this->once())
        ->method('appendTo')
        ->with($this->isInstanceOf(Element::class))
        ->will(
          $this->returnCallback(
            function(Element $parentNode) {
              return $parentNode->appendElement('success');
            }
          )
        );
      $document = new Document();
      $document->appendChild($document->createElement('root'));
      $document->documentElement->append($object);
      $this->assertXmlStringEqualsXmlString(
        '<root><success/></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithText(): void {
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append('success');
      $this->assertXmlStringEqualsXmlString(
        '<root>success</root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithArraySetsAttributes(): void {
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(['result' => 'success']);
      $this->assertXmlStringEqualsXmlString(
        '<root result="success"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithNode(): void {
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(
        $document->createElement('success')
      );
      $this->assertXmlStringEqualsXmlString(
        '<root><success/></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithNodeAppendsClone(): void {
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(
        $document->documentElement
      );
      $this->assertXmlStringEqualsXmlString(
        '<root><root/></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithAttributeNode(): void {
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(
         $document->createAttribute('result', 'success')
      );
      $this->assertXmlStringEqualsXmlString(
        '<root result="success"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithDocument(): void {
      $import = new \DOMDocument();
      $import->loadXml('<success/>');
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(
        $import
      );
      $this->assertXmlStringEqualsXmlString(
        '<root><success/></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithEmptyDocument(): void {
      $import = new \DOMDocument();
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(
        $import
      );
      $this->assertXmlStringEqualsXmlString(
        '<root/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithNodeFromOtherDocument(): void {
      $import = new \DOMDocument();
      $import->loadXml('<success/>');
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(
        $import->documentElement
      );
      $this->assertXmlStringEqualsXmlString(
        '<root><success/></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithNodeListFromOtherDocument(): void {
      $import = new Document();
      $import->loadXml('<success/>');
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(
        $import->evaluate('/*')
      );
      $this->assertXmlStringEqualsXmlString(
        '<root><success/></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithArrayContainingNodes(): void {
      $document = new Document();
      $document
        ->appendElement('root')
        ->append(
          [
            'attr' => 42,
            $document->createComment('success'),
            $document->createCDATASection('success')
          ]
        );
      $this->assertXmlStringEqualsXmlString(
        '<root attr="42"><!--success--><![CDATA[success]]></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithAttributeNodeFromOtherDocument(): void {
      $import = new \DOMDocument();
      $import->loadXml('<root result="success"/>');
      $document = new Document();
      $document->appendElement('root');
      $document->documentElement->append(
        $import->documentElement->getAttributeNode('result')
      );
      $this->assertXmlStringEqualsXmlString(
        '<root result="success"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithClosure(): void {
      $document = new Document();
      $document
        ->appendElement('root')
        ->append(
          function () use ($document) {
            return $document->createCDATASection('success');
          }
        );
      $this->assertXmlStringEqualsXmlString(
        '<root><![CDATA[success]]></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::append
     */
    public function testAppendWithUnattachedNodeExpectingException(): void {
      $node = new Element('foo', '', '');
      $this->expectException(\LogicException::class);
      $node->append('');
    }

    /**
     * @covers \FluentDOM\DOM\Element::appendElement
     */
    public function testAppendElement(): void {
      $document = new Document();
      $document->appendChild($document->createElement('root'));
      $document->documentElement->appendElement('test', 'text', ['attribute' => 'value']);
      $this->assertXmlStringEqualsXmlString(
        '<root><test attribute="value">text</test></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::appendXml
     */
    public function testAppendXml(): void {
      $document = new Document();
      $document->appendChild($document->createElement('root'));
      $document->documentElement->appendXml(
        '<test attribute="value">text</test>'
      );
      $this->assertXmlStringEqualsXmlString(
        '<root><test attribute="value">text</test></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::appendXml
     */
    public function testAppendXmlWithNamespace(): void {
      $document = new Document();
      $document->appendChild($document->createElement('root'));
      $document->documentElement->appendXml(
        '<foo:test xmlns:foo="urn:foo" foo:attribute="value">text</foo:test>'
      );
      $this->assertXmlStringEqualsXmlString(
        '<root><foo:test xmlns:foo="urn:foo" foo:attribute="value">text</foo:test></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::saveXml
     */
    public function testSaveXml(): void {
      $document = new Document();
      $node = $document->appendChild($document->createElement('div'));
      $this->assertXmlStringEqualsXmlString('<div/>', $node->saveXml());
    }

    /**
     * @covers \FluentDOM\DOM\Element::saveXmlFragment
     */
    public function testSaveXmlFragment(): void {
      $document = new Document();
      $node = $document->appendChild($document->createElement('div'));
      $node->appendChild($document->createTextNode("text"));
      $node->appendChild($document->createElement("br"));
      $this->assertEquals('text<br/>', $node->saveXmlFragment());
    }

    /**
     * @covers \FluentDOM\DOM\Element::saveHtml
     */
    public function testSaveHtml(): void {
      $document = new Document();
      $node = $document->appendChild($document->createElement('div'));
      $this->assertEquals('<div></div>', $node->saveHtml());
    }

    /**
     * @covers \FluentDOM\DOM\Element
     * @covers \FluentDOM\DOM\Node\Xpath
     */
    public function testEvaluate(): void {
      $document = new Document();
      $document->loadXml('<foo>success</foo>');
      $this->assertEquals(
        'success',
        $document->documentElement->evaluate('string(.)')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     * @covers \FluentDOM\DOM\Node\Xpath
     */
    public function testEvaluateWithUnattachedNodeExpectingException(): void {
      $node = new Element('foo');
      $this->expectException(\LogicException::class);
      $node->evaluate('string(.)');
    }

    /**
     * @covers \FluentDOM\DOM\Element
     * @covers \FluentDOM\DOM\Node\Xpath
     */
    public function testMagicMethodInvoke(): void {
      $document = new Document();
      $document->loadXml('<foo>success</foo>');
      $node = $document->documentElement;
      $this->assertEquals(
        'success',
        $node('string(.)')
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:getElementsByTagName
     */
    public function testGetElementsByTagNameWithNamespace(): void {
      $document = new Document();
      $document->loadXML('<foo:bar xmlns:foo="urn:foo"><foo:foo></foo:foo></foo:bar>');
      $document->registerNamespace('f', 'urn:foo');
      $this->assertEquals(
        [$document->documentElement->firstChild],
        iterator_to_array($document->documentElement->getElementsByTagName('f:foo'), FALSE)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:getElementsByTagName
     */
    public function testGetElementsByTagName(): void {
      $document = new Document();
      $document->loadXML('<foo><bar></bar></foo>');
      $this->assertEquals(
        [$document->documentElement->firstChild],
        iterator_to_array($document->documentElement->getElementsByTagName('bar'), FALSE)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     * @dataProvider provideExistingOffsets
     */
    public function testArrayAccessOffsetExistsExpectingTrue($offset): void {
      $document = new Document();
      $document->loadXML(self::XML);
      $this->assertTrue(isset($document->documentElement[$offset]));
    }

    public static function provideExistingOffsets() {
      return [
        [0],
        [1],
        ['version']
      ];
    }

    /**
     * @covers \FluentDOM\DOM\Element
     * @dataProvider provideMissingOffsets
     */
    public function testArrayAccessOffsetExistsExpectingFalse($offset): void {
      $document = new Document();
      $document->loadXML(self::XML);
      $this->assertFalse(isset($document->documentElement[$offset]));
    }

    public static function provideMissingOffsets() {
      return [
        [99],
        ['NON_EXISTING']
      ];
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetExistsExpectingException(): void {
      $document = new Document();
      $document->loadXML(self::XML);
      $this->expectException(
        \InvalidArgumentException::class,
        'Invalid offset. Use integer for child nodes and strings for attributes.'
      );
      $document->documentElement[NULL];
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetGetWithItem(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/><foobar/></foo>');
      $this->assertEquals(
        '<foobar/>',
        $document->saveXML($document->documentElement[1])
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetGetWithAttribute(): void {
      $document = new Document();
      $document->loadXML('<foo attribute="success"/>');
      $this->assertEquals(
        'success',
        $document->documentElement['attribute']
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetGetWithChaining(): void {
      $document = new Document();
      $document->loadXML('<foo><bar attribute="success"/></foo>');
      $this->assertEquals(
        'success',
        $document->documentElement[0]['attribute']
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetSetAppendChild(): void {
      $document = new Document();
      $document->appendChild($document->createElement('root'));
      $document->documentElement[] = $document->createElement('success');
      $this->assertEquals(
        '<root><success/></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetSetReplaceChild(): void {
      $document = new Document();
      $document
        ->appendChild($document->createElement('root'))
        ->appendChild($document->createElement('fail'));
      $document->documentElement[0] = $document->createElement('success');
      $this->assertEquals(
        '<root><success/></root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetSetWithInvalidChildExpectingException(): void {
      $document = new Document();
      $document->appendChild($document->createElement('root'));
      $this->expectException(
        \InvalidArgumentException::class,
        '$value is not a valid \DOMNode'
      );
      $document->documentElement[0] = NULL;
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetSetWriteAttribute(): void {
      $document = new Document();
      $document
        ->appendChild($document->createElement('root'));
      $document->documentElement['result'] = 'success';
      $this->assertEquals(
        '<root result="success"/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetUnsetRemoveChild(): void {
      $document = new Document();
      $document->loadXML('<root><fail/></root>');
      unset($document->documentElement[0]);
      $this->assertEquals(
        '<root/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element
     */
    public function testArrayAccessOffsetUnsetRemoveAttribute(): void {
      $document = new Document();
      $document->loadXML('<root result="fail"/>');
      unset($document->documentElement['result']);
      $this->assertEquals(
        '<root/>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::getIterator
     */
    public function testGetIterator(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/><bar/></foo>');
      $this->assertEquals(
        [
          $document->documentElement->firstChild,
          $document->documentElement->firstChild
        ],
        iterator_to_array(
          $document->documentElement
        )
      );
    }

    /**
     * @covers \FluentDOM\DOM\Element::count
     */
    public function testCountable(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $this->assertCount(
        1, $document->documentElement
      );
    }

    /**
     * @param int $expected
     * @param string $xml
     * @covers \FluentDOM\DOM\Element
     * @testWith
     *   [0, "<foo/>"]
     *   [1, "<foo><bar/></foo>"]
     *   [1, "<foo>text<bar/>text</foo>"]
     *   [1, "<foo>text<bar><child/></bar>text</foo>"]
     */
    public function testChildElementCount($expected, $xml) {
      $document = new Document();
      $document->loadXML($xml);
      $this->assertSame($expected, $document->documentElement->childElementCount);
    }

    /**
     * @param string $expected
     * @param string $xml
     * @covers \FluentDOM\DOM\Element::clarkNotation
     * @testWith
     *   ["foo", "<foo/>"]
     *   ["{http://example.com}foo", "<foo xmlns=\"http://example.com\"/>"]
     *   ["{http://example.com}foo", "<bar:foo xmlns:bar=\"http://example.com\"/>"]
     *   ["{urn:example}foo", "<foo xmlns=\"urn:example\"/>"]
     */
    public function testClarkNotation(string $expected, string $xml) {
      $document = new Document();
      $document->loadXML($xml);
      $this->assertSame($expected, $document->documentElement->clarkNotation());
    }
  }
}
