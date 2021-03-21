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

  use FluentDOM\TestCase;

  class DocumentFragmentTest extends TestCase {

     /**
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testMagicMethodToString(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('<test>success</test>');
      $this->assertEquals(
        'success',
        (string)$fragment
      );
    }

     /**
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testFirstElementChild(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testLastElementChild(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testGetIterator(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml(
        'TEXT<test index="1"/>TEXT<test index="2"/>TEXT'
      );
      $array = iterator_to_array($fragment);
      $this->assertCount(5, $array);
    }

    /**
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testCount(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testSaveFragment(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('<test>success</test>success');
      $this->assertEquals(
        '<test>success</test>success',
        (string)$fragment->saveXmlFragment()
      );
    }

     /**
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testSaveFragmentAddsNamespaces(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('<test>success</test>success', ['#default' => 'urn:default']);
      $this->assertEquals(
        '<test xmlns="urn:default">success</test>success',
        (string)$fragment->saveXmlFragment()
      );
    }

     /**
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithoutNamespaces(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithNamespacesFromDocument(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithDefaultNamespace(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithNamespacesFromElementNode(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithDefaultNamespaceFromElementNode(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithNamespacesList(): void {
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
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithInvalidNamespacesListExpectingException(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $this->expectException(
        \InvalidArgumentException::class,
        '$namespaces needs to be a list of namespaces or an element node to fetch the namespaces from.'
      );
      $fragment->appendXml('<foo:test>success</foo:test>',  'INVALID_VALUE');
    }

    /**
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithInvalidNamespacesExpectingException(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $this->expectException(
        \InvalidArgumentException::class
      );
      $fragment->appendXml('<foo:test>success</foo:test>', FALSE);
    }

    /**
     * @covers \FluentDOM\DOM\DocumentFragment
     */
    public function testWithInvalidFragmentReturningFalse(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $this->assertFalse(
        @$fragment->appendXml('<test success</test>', ['foo' => 'urn:bar'])
      );
    }

    public function testAppendElement(): void {
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
