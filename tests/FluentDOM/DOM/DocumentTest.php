<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM {

  require_once __DIR__ . '/../TestCase.php';

  use FluentDOM\TestCase;

  class DocumentTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Document
     */
    public function testClone(): void {
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
     * @covers \FluentDOM\DOM\Document::__construct
     */
    public function testDocumentRegistersNodeClass(): void {
      $document = new Document();
      $node = $document->appendElement('test');
      $this->assertInstanceOf(
        Element::class,
        $node,
        "Node class registration failed for createElement."
      );
    }
    /**
     * @covers \FluentDOM\DOM\Document::__construct
     */
    public function testDocumentRegistersNodeClassLoadingXml(): void {
      $document = new Document();
      $document->appendElement('test');
      $this->assertInstanceOf(
        Element::class,
        $document->documentElement,
        "Node class registration failed."
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::xpath
     */
    public function testXpathImplicitCreate(): void {
      $document = new Document();
      $xpath = $document->xpath();
      $this->assertSame($xpath, $document->xpath());
    }

    /**
     * @covers \FluentDOM\DOM\Document::xpath
     */
    public function testXpathImplicitCreateAfterDocumentLoad(): void {
      $document = new Document();
      $xpath = $document->xpath();
      $document->loadXML('<test/>');
      $this->assertNotSame($xpath, $document->xpath());
    }

    /**
     * @covers \FluentDOM\DOM\Document::registerNamespace
     * @covers \FluentDOM\DOM\Document::xpath
     */
    public function testNamespaceIsRegisteredOnExistingXpath(): void {
      $document = new Document();
      $document->loadXML('<test xmlns:foo="urn:foo" foo:result="success"/>');
      $xpath = $document->xpath();
      $document->registerNamespace('bar', 'urn:foo');
      $this->assertEquals(
        'success', $xpath->evaluate('string(/test/@bar:result)')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::registerNamespace
     * @covers \FluentDOM\DOM\Document::xpath
     */
    public function testNamespaceIsRegisteredOnNewXpath(): void {
      $document = new Document();
      $document->loadXML('<test xmlns:foo="urn:foo" foo:result="success"/>');
      $document->registerNamespace('bar', 'urn:foo');
      $this->assertEquals(
        'success', $document->xpath()->evaluate('string(/test/@bar:result)')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::namespaces
     */
    public function testNamespacesGet(): void {
      $document = new Document();
      $document->registerNamespace('#default', 'urn:default');
      $document->registerNamespace('foo', 'urn:foo');
      $this->assertEquals(
        [
          '#default' => 'urn:default',
          'foo' => 'urn:foo'
        ],
        iterator_to_array($document->namespaces())
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::namespaces
     */
    public function testNamespacesSet(): void {
      $document = new Document();
      $document->registerNamespace('foo', 'urn:foo');
      $document->namespaces(
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
        iterator_to_array($document->namespaces())
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendContent
     * @covers \FluentDOM\DOM\Document::appendAttributes
     */
    public function testCreateElementWithoutNamespace(): void {
      $document = new Document();
      $document->registerNamespace('#default', 'urn:default');
      $document->appendChild($document->createElement(':example'));
      $this->assertXmlStringEqualsXmlString(
        '<example xmlns=""/>',
        $document->saveXml($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendContent
     * @covers \FluentDOM\DOM\Document::appendAttributes
     */
    public function testCreateElementWithContent(): void {
      $document = new Document();
      $document->appendChild($document->createElement('example', 'Content & More'));
      $this->assertXmlStringEqualsXmlString(
        '<example>Content &amp; More</example>',
        $document->saveXml($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendContent
     * @covers \FluentDOM\DOM\Document::appendAttributes
     */
    public function testCreateElementWithZeroContent(): void {
      $document = new Document();
      $document->appendChild($document->createElement('example', '0'));
      $this->assertXmlStringEqualsXmlString(
        '<example>0</example>',
        $document->saveXml($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendContent
     * @covers \FluentDOM\DOM\Document::appendAttributes
     */
    public function testCreateElementWithContentAndAttribute(): void {
      $document = new Document();
      $document->appendChild(
        $document->createElement('example', 'Content & More', ['attr' => 'value'])
      );
      $this->assertXmlStringEqualsXmlString(
        '<example attr="value">Content &amp; More</example>',
        $document->saveXml($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendContent
     * @covers \FluentDOM\DOM\Document::appendAttributes
     */
    public function testCreateElementWithAttributeAsSecondArgument(): void {
      $document = new Document();
      $document->appendChild(
        $document->createElement('example', ['attr' => 'value'])
      );
      $this->assertXmlStringEqualsXmlString(
        '<example attr="value"/>',
        $document->saveXml($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendContent
     * @covers \FluentDOM\DOM\Document::appendAttributes
     */
    public function testCreateElementWithAttributeAsSecondAndThirdArgument(): void {
      $document = new Document();
      $document->appendChild(
        $document->createElement('example', ['attr1' => 'one'], ['attr2' => 'two'])
      );
      $this->assertXmlStringEqualsXmlString(
        '<example attr1="one" attr2="two"/>',
        $document->saveXml($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendContent
     * @covers \FluentDOM\DOM\Document::appendAttributes
     */
    public function testCreateElementWithNamespace(): void {
      $document = new Document();
      $document->registerNamespace('test', 'urn:success');
      $document->appendChild($document->createElement('test:example'));
      $this->assertXmlStringEqualsXmlString(
        '<test:example xmlns:test="urn:success"/>',
        $document->saveXml($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendContent
     * @covers \FluentDOM\DOM\Document::appendAttributes
     */
    public function testCreateElementWithXmlNamespacePrefixExpectingException(): void {
      $document = new Document();
      $this->expectException(\LogicException::class);
      $this->expectExceptionMessage('Can not use reserved namespace prefix "xml" in element name');
      $document->appendChild($document->createElement('xml:example'));
    }

    /**
     * @covers \FluentDOM\DOM\Document::createElementNs
     * @covers \FluentDOM\DOM\Document::appendContent
     */
    public function testCreateElementNsWithContent(): void {
      $document = new Document();
      $document->appendChild(
        $document->createElementNs('urn:default', 'example', 'Content & More')
      );
      $this->assertXmlStringEqualsXmlString(
        '<example xmlns="urn:default">Content &amp; More</example>',
        $document->saveXml($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createAttribute
     */
    public function testCreateAttribute(): void {
      $document = new Document();
      /** @var Element $node */
      $node = $document->appendChild($document->createElement('example'));
      $node->appendChild($document->createAttribute('attribute'));
      $this->assertXmlStringEqualsXmlString(
        '<example attribute=""/>',
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createAttribute
     */
    public function testCreateAttributeWithNamespace(): void {
      $document = new Document();
      $document->registerNamespace('test', 'urn:success');
      /** @var Element $node */
      $node = $document->appendChild($document->createElement('example'));
      $node->appendChild($document->createAttribute('test:attribute', 'success'));
      $this->assertXmlStringEqualsXmlString(
        '<example xmlns:test="urn:success" test:attribute="success"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::__construct
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendElement
     */
    public function testAppendElement(): void {
      $document = new Document();
      $document->appendElement('test', 'text', ['attribute' => 'value']);
      $this->assertXmlStringEqualsXmlString(
        '<test attribute="value">text</test>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::__construct
     * @covers \FluentDOM\DOM\Document::createElement
     * @covers \FluentDOM\DOM\Document::appendElement
     */
    public function testAppendElementWithNamespace(): void {
      $document = new Document();
      $document->registerNamespace('foo', 'urn:foo');
      $document->appendElement('foo:test', 'text', ['foo:attribute' => 'value']);
      $this->assertXmlStringEqualsXmlString(
        '<foo:test xmlns:foo="urn:foo" foo:attribute="value">text</foo:test>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::evaluate
     */
    public function testEvaluate(): void {
      $document = new Document();
      $document->loadXml('<foo>success</foo>');
      $this->assertEquals(
        'success',
        $document->evaluate('string(/foo)')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::evaluate
     */
    public function testEvaluateWithContext(): void {
      $document = new Document();
      $document->loadXml('<foo>success</foo>');
      $this->assertEquals(
        'success',
        $document->evaluate('string(.)', $document->documentElement)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:toXml
     */
    public function testToXmlWithoutContext(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'."\n".'<foo><bar/></foo>'."\n",
        $document->toXml()
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:toXml
     */
    public function testToXmlWithNodeContext(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $this->assertEquals(
        '<foo><bar/></foo>',
        $document->toXml($document->documentElement)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:toXml
     */
    public function testToXmlWithNodeListContext(): void {
      $document = new Document();
      $document->loadXML('<foo>TEXT<bar/></foo>');
      $this->assertEquals(
        'TEXT<bar/>',
        $document->toXml($document->documentElement->childNodes)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:toHtml
     */
    public function testToHtmlWithoutContext(): void {
      $document = new Document();
      $document->loadXML('<div>TEXT</div>');
      $this->assertEquals(
        "<div>TEXT</div>\n",
        $document->toHtml()
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:toHtml
     */
    public function testToHtmlWithNodeContext(): void {
      $document = new Document();
      $document->loadXML("<div>TEXT<br/>\n</div>");
      $this->assertEquals(
        "<div>TEXT<br>\n</div>",
        $document->toHtml($document->firstChild)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:toHtml
     */
    public function testToHtmlWithNodeListContext(): void {
      $document = new Document();
      $document->loadXML('<div>TEXT<br/></div>');
      $this->assertEquals(
        "TEXT<br>",
        $document->toHtml($document->firstChild->childNodes)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:saveHTML
     */
    public function testSaveHtmlWithNodeListContext(): void {
      $document = new Document();
      $document->loadXML('<div>TEXT<br/></div>');
      $this->assertEquals(
        "TEXT<br>",
        $document->saveHtml($document->firstChild->childNodes)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:saveHTML
     */
    public function testSaveHtmlWithDocumentFragmentContext(): void {
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
     * @cover FluentDOM\DOM\Document:getElementsByTagName
     */
    public function testGetElementsByTagNameWithNamespace(): void {
      $document = new Document();
      $document->loadXML('<foo:bar xmlns:foo="urn:foo"/>');
      $document->registerNamespace('f', 'urn:foo');
      $this->assertEquals(
        [$document->documentElement],
        iterator_to_array($document->getElementsByTagName('f:bar'), FALSE)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document:getElementsByTagName
     */
    public function testGetElementsByTagName(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertEquals(
        [$document->documentElement],
        iterator_to_array($document->getElementsByTagName('foo'), FALSE)
      );
    }

    /**
     * @cover FluentDOM\DOM\Document::__toString
     */
    public function testStringCast(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertEquals(
        "<?xml version=\"1.0\"?>\n<foo/>\n",
        (string)$document
      );
    }

    /**
     * @covers \FluentDOM\DOM\Document::createDocumentType
     */
    public function testCreateDocumentType(): void {
      $document = new Document();
      $document->formatOutput = TRUE;
      $document->appendChild($document->createDocumentType('html'));
      $document->appendElement('html');
      $this->assertEquals(
        "<!DOCTYPE html>\n<html></html>\n",
        $document->saveHTML()
      );
    }


    /**
     * @covers \FluentDOM\DOM\Document::createDocumentType
     */
    public function testCreateDocumentTypeXhtmlStrict(): void {
      $document = new Document();
      $document->formatOutput = TRUE;
      $document->appendChild(
        $document->createDocumentType(
          'html',
          '-//W3C//DTD XHTML 1.0 Strict//EN',
          'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'
        )
      );
      $document->appendElement('html');
      $this->assertEquals(
        "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n<html></html>\n",
        $document->saveHTML()
      );
    }
  }
}
