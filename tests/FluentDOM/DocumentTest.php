<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class DocumentTest extends TestCase {

    /**
     * @covers FluentDOM\Document::__construct
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
     * @covers FluentDOM\Document::__construct
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
     * @covers FluentDOM\Document::xpath
     */
    public function testXpathImplicitCreate() {
      $dom = new Document();
      $xpath = $dom->xpath();
      $this->assertInstanceOf(__NAMESPACE__.'\\Xpath', $xpath);
      $this->assertSame($xpath, $dom->xpath());
    }

    /**
     * @covers FluentDOM\Document::xpath
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
     * @covers FluentDOM\Document::registerNamespace
     * @covers FluentDOM\Document::xpath
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
     * @covers FluentDOM\Document::registerNamespace
     * @covers FluentDOM\Document::xpath
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
     * @covers FluentDOM\Document::registerNamespace
     * @covers FluentDOM\Document::getNamespace
     * @covers FluentDOM\Document::validatePrefix
     */
    public function testGetNamespaceAfterRegister() {
      $dom = new Document();
      $dom->registerNamespace('test', 'urn:success');
      $this->assertEquals(
        'urn:success',
        $dom->getNamespace('test')
      );
    }

    /**
     * @covers FluentDOM\Document::registerNamespace
     * @covers FluentDOM\Document::getNamespace
     * @covers FluentDOM\Document::validatePrefix
     */
    public function testGetDefaultNamespaceAfterRegister() {
      $dom = new Document();
      $dom->registerNamespace('#default', 'urn:success');
      $this->assertEquals(
        'urn:success',
        $dom->getNamespace('')
      );
    }

    /**
     * @covers FluentDOM\Document::registerNamespace
     * @covers FluentDOM\Document::getNamespace
     * @covers FluentDOM\Document::validatePrefix
     */
    public function testGetDefaultNamespaceWithoutRegister() {
      $dom = new Document();
      $this->assertEquals(
        '',
        $dom->getNamespace('#default')
      );
    }

    /**
     * @covers FluentDOM\Document::registerNamespace
     * @covers FluentDOM\Document::getNamespace
     * @covers FluentDOM\Document::validatePrefix
     */
    public function testRegisterReservedNamespaceExpectingException() {
      $dom = new Document();
      $this->setExpectedException(
        'LogicException',
        'Can not register reserved namespace prefix "xml".'
      );
      $dom->registerNamespace('xml', 'urn:fail');
    }

    /**
     * @covers FluentDOM\Document::getNamespace
     * @covers FluentDOM\Document::validatePrefix
     */
    public function testGetReservedNamespace() {
      $dom = new Document();
      $this->assertEquals(
        'http://www.w3.org/XML/1998/namespace',
        $dom->getNamespace('xml')
      );
    }

    /**
     * @covers FluentDOM\Document::getNamespace
     * @covers FluentDOM\Document::validatePrefix
     */
    public function testGetNamespaceWithoutRegisterExpectingException() {
      $dom = new Document();
      $this->setExpectedException(
        'LogicException',
        'Unknown namespace prefix "test".'
      );
      $dom->getNamespace('test');
    }

    /**
     * @covers FluentDOM\Document::namespaces
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
        $dom->namespaces()
      );
    }

    /**
     * @covers FluentDOM\Document::namespaces
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
        $dom->namespaces()
      );
    }

    /**
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::ensureElement
     * @covers FluentDOM\Document::appendContent
     * @covers FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithoutNamespace() {
      $dom = new Document();
      $dom->registerNamespace('#default', 'urn:default');
      $dom->appendChild($dom->createElement(':example'));
      $this->assertEquals(
        '<example xmlns=""/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::ensureElement
     * @covers FluentDOM\Document::appendContent
     * @covers FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithContent() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('example', 'Content & More'));
      $this->assertEquals(
        '<example>Content &amp; More</example>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::ensureElement
     * @covers FluentDOM\Document::appendContent
     * @covers FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithContentAndAttribute() {
      $dom = new Document();
      $dom->appendChild(
        $dom->createElement('example', 'Content & More', ['attr' => 'value'])
      );
      $this->assertEquals(
        '<example attr="value">Content &amp; More</example>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::ensureElement
     * @covers FluentDOM\Document::appendContent
     * @covers FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithAttributeAsSecondArgument() {
      $dom = new Document();
      $dom->appendChild(
        $dom->createElement('example', ['attr' => 'value'])
      );
      $this->assertEquals(
        '<example attr="value"/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::ensureElement
     * @covers FluentDOM\Document::appendContent
     * @covers FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithAttributeAsSecondAndThirdArgument() {
      $dom = new Document();
      $dom->appendChild(
        $dom->createElement('example', ['attr1' => 'one'], ['attr2' => 'two'])
      );
      $this->assertEquals(
        '<example attr1="one" attr2="two"/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::ensureElement
     * @covers FluentDOM\Document::appendContent
     * @covers FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithNamespace() {
      $dom = new Document();
      $dom->registerNamespace('test', 'urn:success');
      $dom->appendChild($dom->createElement('test:example'));
      $this->assertEquals(
        '<test:example xmlns:test="urn:success"/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::ensureElement
     * @covers FluentDOM\Document::appendContent
     * @covers FluentDOM\Document::appendAttributes
     */
    public function testCreateElementWithXmlNamespacePrefixExpectingException() {
      $dom = new Document();
      $this->setExpectedException(
        'LogicException',
        'Can not use reserved namespace prefix "xml" in element name'
      );
      $dom->appendChild($dom->createElement('xml:example'));
    }

    /**
     * @covers FluentDOM\Document::createElementNs
     * @covers FluentDOM\Document::ensureElement
     * @covers FluentDOM\Document::appendContent
     */
    public function testCreateElementNsWithContent() {
      $dom = new Document();
      $dom->appendChild(
        $dom->createElementNs('urn:default', 'example', 'Content & More')
      );
      $this->assertEquals(
        '<example xmlns="urn:default">Content &amp; More</example>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::createAttribute
     */
    public function testCreateAttribute() {
      $dom = new Document();
      $node = $dom->appendChild($dom->createElement('example'));
      $node->appendChild($dom->createAttribute('attribute'));
      $this->assertEquals(
        '<example attribute=""/>',
        $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Document::createAttribute
     */
    public function testCreateAttributeWithNamespace() {
      $dom = new Document();
      $dom->registerNamespace('test', 'urn:success');
      $node = $dom->appendChild($dom->createElement('example'));
      $node->appendChild($dom->createAttribute('test:attribute', 'success'));
      $this->assertEquals(
        '<example xmlns:test="urn:success" test:attribute="success"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Document::__construct
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::appendElement
     */
    public function testAppendElement() {
      $dom = new Document();
      $dom->appendElement('test', 'text', array('attribute' => 'value'));
      $this->assertEquals(
        '<test attribute="value">text</test>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::__construct
     * @covers FluentDOM\Document::createElement
     * @covers FluentDOM\Document::appendElement
     */
    public function testAppendElementWithNamespace() {
      $dom = new Document();
      $dom->registerNamespace('foo', 'urn:foo');
      $dom->appendElement('foo:test', 'text', array('foo:attribute' => 'value'));
      $this->assertEquals(
        '<foo:test xmlns:foo="urn:foo" foo:attribute="value">text</foo:test>',
        $dom->saveXML($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::evaluate
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
     * @covers FluentDOM\Document::evaluate
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
      $this->assertInstanceOf('FluentDOM\Query', $fd);
      $this->assertSame(
        $dom->documentElement->firstChild,
        $fd[0]
      );
    }
  }
}
