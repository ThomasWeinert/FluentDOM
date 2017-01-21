<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class DocumentTest extends TestCase {

    /**
     * @covers \FluentDOM\Document
     */
    public function testClone() {
      $document = new Document();
      $document->registerNamespace('foo', 'urn:foo');
      $clone = clone $document;
      $this->assertNotSame($document->namespaces(), $clone->namespaces());
      $this->assertEquals(
        ['foo' => 'urn:foo'],
        iterator_to_array($clone->namespaces())
      );
    }

    /**
     * @covers \FluentDOM\Document::__construct
     */
    public function testDocumentRegistersNodeClass() {
      $dom = new Document();
      $node = $dom->appendElement('test');
      $this->assertInstanceOf(
        'FluentDOM\\Element',
        $node,
        "Node class registration failed for createElement."
      );
    }
    /**
     * @covers \FluentDOM\Document::__construct
     */
    public function testDocumentRegistersNodeClassLoadingXml() {
      $dom = new Document();
      $dom->appendElement('test');
      $this->assertInstanceOf(
        'FluentDOM\\Element',
        $dom->documentElement,
        "Node class registration failed."
      );
    }

    /**
     * @covers \FluentDOM\Document::xpath
     */
    public function testXpathImplicitCreate() {
      $dom = new Document();
      $xpath = $dom->xpath();
      $this->assertInstanceOf(__NAMESPACE__.'\\Xpath', $xpath);
      $this->assertSame($xpath, $dom->xpath());
    }

    /**
     * @covers \FluentDOM\Document::xpath
     */
    public function testXpathImplicitCreateAfterDocumentLoad() {
      if (defined('HHVM_VERSION')) {
        $this->markTestSkipped(
          'HHVM does not need to recreate the Xpath instance.'
        );
      }
      $dom = new Document();
      $xpath = $dom->xpath();
      $dom->loadXML('<test/>');
      $this->assertInstanceOf(__NAMESPACE__.'\\Xpath', $xpath);
      $this->assertNotSame($xpath, $dom->xpath());
    }

    /**
     * @covers \FluentDOM\Document::registerNamespace
     * @covers \FluentDOM\Document::xpath
     */
    public function testNamespaceIsRegisteredOnExistingXpath() {
      $dom = new Document();
      $dom->loadXML('<test xmlns:foo="urn:foo" foo:result="success"/>');
      $xpath = $dom->xpath();
      $dom->registerNamespace('bar', 'urn:foo');
      $this->assertEquals(
        'success', $xpath->evaluate('string(/test/@bar:result)')
      );
    }

    /**
     * @covers \FluentDOM\Document::registerNamespace
     * @covers \FluentDOM\Document::xpath
     */
    public function testNamespaceIsRegisteredOnNewXpath() {
      $dom = new Document();
      $dom->loadXML('<test xmlns:foo="urn:foo" foo:result="success"/>');
      $dom->registerNamespace('bar', 'urn:foo');
      $this->assertEquals(
        'success', $dom->xpath()->evaluate('string(/test/@bar:result)')
      );
    }

    /**
     * @covers \FluentDOM\Document::namespaces
     */
    public function testNamespacesGet() {
      $dom = new Document();
      $dom->registerNamespace('#default', 'urn:default');
      $dom->registerNamespace('foo', 'urn:foo');
      $this->assertEquals(
        [
          '#default' => 'urn:default',
          'foo' => 'urn:foo'
        ],
        iterator_to_array($dom->namespaces())
      );
    }

    /**
     * @covers \FluentDOM\Document::namespaces
     */
    public function testNamespacesSet() {
      $dom = new Document();
      $dom->registerNamespace('foo', 'urn:foo');
      $dom->namespaces(
        [
          '#default' => 'urn:default',
          'bar' => 'urn:bar',
        ]
      );
      $this->assertEquals(
        [
          '#default' => 'urn:default',
          'bar' => 'urn:bar'
        ],
        iterator_to_array($dom->namespaces())
      );
    }

    /**
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendContent
     * @covers \FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithoutNamespace() {
      $dom = new Document();
      $dom->registerNamespace('#default', 'urn:default');
      $dom->appendChild($dom->createElement(':example'));
      $this->assertXmlStringEqualsXmlString(
        '<example xmlns=""/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendContent
     * @covers \FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithContent() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('example', 'Content & More'));
      $this->assertXmlStringEqualsXmlString(
        '<example>Content &amp; More</example>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendContent
     * @covers \FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithZeroContent() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('example', '0'));
      $this->assertXmlStringEqualsXmlString(
        '<example>0</example>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendContent
     * @covers \FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithContentAndAttribute() {
      $dom = new Document();
      $dom->appendChild(
        $dom->createElement('example', 'Content & More', ['attr' => 'value'])
      );
      $this->assertXmlStringEqualsXmlString(
        '<example attr="value">Content &amp; More</example>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendContent
     * @covers \FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithAttributeAsSecondArgument() {
      $dom = new Document();
      $dom->appendChild(
        $dom->createElement('example', ['attr' => 'value'])
      );
      $this->assertXmlStringEqualsXmlString(
        '<example attr="value"/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendContent
     * @covers \FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithAttributeAsSecondAndThirdArgument() {
      $dom = new Document();
      $dom->appendChild(
        $dom->createElement('example', ['attr1' => 'one'], ['attr2' => 'two'])
      );
      $this->assertXmlStringEqualsXmlString(
        '<example attr1="one" attr2="two"/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendContent
     * @covers \FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithNamespace() {
      $dom = new Document();
      $dom->registerNamespace('test', 'urn:success');
      $dom->appendChild($dom->createElement('test:example'));
      $this->assertXmlStringEqualsXmlString(
        '<test:example xmlns:test="urn:success"/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendContent
     * @covers \FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithXmlNamespacePrefixExpectingException() {
      $dom = new Document();
      $this->expectException(
        \LogicException::class,
        'Can not use reserved namespace prefix "xml" in element name'
      );
      $dom->appendChild($dom->createElement('xml:example'));
    }

    /**
     * @covers \FluentDOM\Document::createElementNs
     * @covers \FluentDOM\Document::appendContent
     */
    public function testCreateElementNsWithContent() {
      $dom = new Document();
      $dom->appendChild(
        $dom->createElementNs('urn:default', 'example', 'Content & More')
      );
      $this->assertXmlStringEqualsXmlString(
        '<example xmlns="urn:default">Content &amp; More</example>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::createAttribute
     */
    public function testCreateAttribute() {
      $dom = new Document();
      /** @var Element $node */
      $node = $dom->appendChild($dom->createElement('example'));
      $node->appendChild($dom->createAttribute('attribute'));
      $this->assertXmlStringEqualsXmlString(
        '<example attribute=""/>',
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Document::createAttribute
     */
    public function testCreateAttributeWithNamespace() {
      $dom = new Document();
      $dom->registerNamespace('test', 'urn:success');
      /** @var Element $node */
      $node = $dom->appendChild($dom->createElement('example'));
      $node->appendChild($dom->createAttribute('test:attribute', 'success'));
      $this->assertXmlStringEqualsXmlString(
        '<example xmlns:test="urn:success" test:attribute="success"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Document::__construct
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendElement
     */
    public function testAppendElement() {
      $dom = new Document();
      $dom->appendElement('test', 'text', array('attribute' => 'value'));
      $this->assertXmlStringEqualsXmlString(
        '<test attribute="value">text</test>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::__construct
     * @covers \FluentDOM\Document::createElement
     * @covers \FluentDOM\Document::appendElement
     */
    public function testAppendElementWithNamespace() {
      $dom = new Document();
      $dom->registerNamespace('foo', 'urn:foo');
      $dom->appendElement('foo:test', 'text', array('foo:attribute' => 'value'));
      $this->assertXmlStringEqualsXmlString(
        '<foo:test xmlns:foo="urn:foo" foo:attribute="value">text</foo:test>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Document::evaluate
     */
    public function testEvaluate() {
      $dom = new Document();
      $dom->loadXml('<foo>success</foo>');
      $this->assertEquals(
        'success',
        $dom->evaluate('string(/foo)')
      );
    }

    /**
     * @covers \FluentDOM\Document::evaluate
     */
    public function testEvaluateWithContext() {
      $dom = new Document();
      $dom->loadXml('<foo>success</foo>');
      $this->assertEquals(
        'success',
        $dom->evaluate('string(.)', $dom->documentElement)
      );
    }

    /**
     * @cover FluentDOM\Document:find
     */
    public function testFind() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $fd = $dom->find('/foo/bar');
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertSame(
        $dom->documentElement->firstChild,
        $fd[0]
      );
    }

    /**
     * @cover FluentDOM\Document:toXml
     */
    public function testToXmlWithoutContext() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'."\n".'<foo><bar/></foo>'."\n",
        $dom->toXml()
      );
    }

    /**
     * @cover FluentDOM\Document:toXml
     */
    public function testToXmlWithNodeContext() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $this->assertEquals(
        '<foo><bar/></foo>',
        $dom->toXml($dom->documentElement)
      );
    }

    /**
     * @cover FluentDOM\Document:toXml
     */
    public function testToXmlWithNodeListContext() {
      $dom = new Document();
      $dom->loadXML('<foo>TEXT<bar/></foo>');
      $this->assertEquals(
        'TEXT<bar/>',
        $dom->toXml($dom->documentElement->childNodes)
      );
    }

    /**
     * @cover FluentDOM\Document:toHtml
     */
    public function testToHtmlWithoutContext() {
      $dom = new Document();
      $dom->loadXML('<div>TEXT</div>');
      $this->assertEquals(
        "<div>TEXT</div>\n",
        $dom->toHtml()
      );
    }

    /**
     * @cover FluentDOM\Document:toHtml
     */
    public function testToHtmlWithNodeContext() {
      $dom = new Document();
      $dom->loadXML('<div>TEXT<br/></div>');
      $this->assertEquals(
        "<div>TEXT<br>\n</div>",
        $dom->toHtml($dom->firstChild)
      );
    }

    /**
     * @cover FluentDOM\Document:toHtml
     */
    public function testToHtmlWithNodeListContext() {
      $dom = new Document();
      $dom->loadXML('<div>TEXT<br/></div>');
      $this->assertEquals(
        "TEXT<br>",
        $dom->toHtml($dom->firstChild->childNodes)
      );
    }

    /**
     * @cover FluentDOM\Document:saveHTML
     */
    public function testSaveHtmlWithNodeListContext() {
      $dom = new Document();
      $dom->loadXML('<div>TEXT<br/></div>');
      $this->assertEquals(
        "TEXT<br>",
        $dom->saveHtml($dom->firstChild->childNodes)
      );
    }

    /**
     * @cover FluentDOM\Document:saveHTML
     */
    public function testSaveHtmlWithDocumentFragmentContext() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendChild($document->createElement('em', 'test'));
      $fragment->appendChild($document->createTextNode('test'));

      $this->assertEquals(
        "<em>test</em>test",
        $document->saveHtml($fragment)
      );
    }

    /**
     * @cover FluentDOM\Document:getElementsByTagName
     */
    public function testGetElementsByTagNameWithNamespace() {
      $dom = new Document();
      $dom->loadXML('<foo:bar xmlns:foo="urn:foo"/>');
      $dom->registerNamespace('f', 'urn:foo');
      $this->assertEquals(
        [$dom->documentElement],
        iterator_to_array($dom->getElementsByTagName('f:bar'), FALSE)
      );
    }

    /**
     * @cover FluentDOM\Document:getElementsByTagName
     */
    public function testGetElementsByTagName() {
      $dom = new Document();
      $dom->loadXML('<foo/>');
      $this->assertEquals(
        [$dom->documentElement],
        iterator_to_array($dom->getElementsByTagName('foo'), FALSE)
      );
    }
  }
}
