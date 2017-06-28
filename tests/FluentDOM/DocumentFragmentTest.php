<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class DocumentFragmentTest extends TestCase {

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testMagicMethodToString() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('<test>success</test>');
      $this->assertEquals(
        'success',
        (string)$fragment
      );
    }

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testFirstElementChild() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml(
        'TEXT<test index="1"/>TEXT<test index="2"/>TEXT'
      );
      $this->assertEquals(
        '<test index="1"/>',
        $fragment->firstElementChild->saveXml()
      );
    }

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testLastElementChild() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml(
        'TEXT<test index="1"/>TEXT<test index="2"/>TEXT'
      );
      $this->assertEquals(
        '<test index="2"/>',
        $fragment->lastElementChild->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testGetIterator() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml(
        'TEXT<test index="1"/>TEXT<test index="2"/>TEXT'
      );
      $array = iterator_to_array($fragment);
      $this->assertCount(5, $array);
    }

    /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testCount() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml(
        'TEXT<test index="1"/>TEXT<test index="2"/>TEXT'
      );
      $this->assertCount(
        5, $fragment
      );
    }

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testSaveFragment() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('<test>success</test>success');
      $this->assertEquals(
        '<test>success</test>success',
        (string)$fragment->saveXmlFragment()
      );
    }

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testSaveFragmentAddsNamespaces() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('<test>success</test>success', ['#default' => 'urn:default']);
      $this->assertEquals(
        '<test xmlns="urn:default">success</test>success',
        (string)$fragment->saveXmlFragment()
      );
    }

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testWithoutNamespaces() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $this->assertTrue(
        $fragment->appendXml('<test>success</test>')
      );
      $document->appendChild($fragment);
      $this->assertEquals(
        '<test>success</test>',
        $document->saveXML($document->documentElement)
      );
    }

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testWithNamespacesFromDocument() {
      $document = new Document();
      $document->registerNamespace('bar', 'urn:bar');
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('<bar:test>success</bar:test>');
      $document->appendChild($fragment);
      $this->assertEquals(
        '<bar:test xmlns:bar="urn:bar">success</bar:test>',
        $document->saveXML($document->documentElement)
      );
    }

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testWithDefaultNamespace() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->registerNamespace('', 'urn:bar');
      $fragment->appendXml('<test>success</test>');
      $document->appendChild($fragment);
      $this->assertEquals(
        '<test xmlns="urn:bar">success</test>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testWithNamespacesFromElementNode() {
      $document = new Document();
      $document->appendChild($document->createElementNS('urn:bar', 'bar:root'));
      $fragment = $document->createDocumentFragment();
      $fragment->namespaces($document->documentElement);
      $fragment->appendXml('<bar:test>success</bar:test>');
      $document->documentElement->appendChild($fragment);
      $this->assertEquals(
        '<bar:root xmlns:bar="urn:bar"><bar:test>success</bar:test></bar:root>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testWithDefaultNamespaceFromElementNode() {
      $document = new Document();
      $document->appendChild($document->createElementNS('urn:bar', 'root'));
      $fragment = $document->createDocumentFragment();
      $fragment->namespaces($document->documentElement);
      $fragment->appendXml('<test>success</test>');
      $document->documentElement->appendChild($fragment);
      $this->assertEquals(
        '<root xmlns="urn:bar"><test>success</test></root>',
        $document->saveXML($document->documentElement)
      );
    }

     /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testWithNamespacesList() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('<foo:test>success</foo:test>', ['foo' => 'urn:bar']);
      $document->appendChild($fragment);
      $this->assertEquals(
        '<foo:test xmlns:foo="urn:bar">success</foo:test>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testWithInvalidNamespacesExpectingException() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $this->expectException(
        \InvalidArgumentException::class
      );
      $fragment->appendXml('<foo:test>success</foo:test>', FALSE);
    }

    /**
     * @covers \FluentDOM\DocumentFragment
     */
    public function testWithInvalidFragmentReturningFalse() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $this->assertFalse(
        @$fragment->appendXml('<test success</test>', ['foo' => 'urn:bar'])
      );
    }

    public function testAppendElement() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendElement('name', 'content', ['attribute' => 'value']);
      $this->assertXmlStringEqualsXmlString(
        '<name attribute="value">content</name>',
        $fragment->saveXmlFragment()
      );

    }
  }
}