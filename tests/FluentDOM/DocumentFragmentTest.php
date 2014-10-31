<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class DocumentFragmentTest extends TestCase {

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testMagicMethodToString() {
      $dom = new Document();
      $fragment = $dom->createDocumentFragment();
      $fragment->appendXml('<test>success</test>');
      $this->assertEquals(
        'success',
        (string)$fragment
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testFirstElementChild() {
      $dom = new Document();
      $fragment = $dom->createDocumentFragment();
      $fragment->appendXml(
        'TEXT<test index="1"/>TEXT<test index="2"/>TEXT'
      );
      $this->assertEquals(
        '<test index="1"/>',
        $fragment->firstElementChild->saveXml()
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testLastElementChild() {
      $dom = new Document();
      $fragment = $dom->createDocumentFragment();
      $fragment->appendXml(
        'TEXT<test index="1"/>TEXT<test index="2"/>TEXT'
      );
      $this->assertEquals(
        '<test index="2"/>',
        $fragment->lastElementChild->saveXml()
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testSaveFragment() {
      $dom = new Document();
      $fragment = $dom->createDocumentFragment();
      $fragment->appendXml('<test>success</test>success');
      $this->assertEquals(
        '<test>success</test>success',
        (string)$fragment->saveXmlFragment()
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testSaveFragmentAddsNamespaces() {
      $dom = new Document();
      $fragment = $dom->createDocumentFragment();
      $fragment->appendXml('<test>success</test>success', ['#default' => 'urn:default']);
      $this->assertEquals(
        '<test xmlns="urn:default">success</test>success',
        (string)$fragment->saveXmlFragment()
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testWithoutNamespaces() {
      $dom = new Document();
      $fragment = $dom->createDocumentFragment();
      $fragment->appendXml('<test>success</test>');
      $dom->appendChild($fragment);
      $this->assertEquals(
        '<test>success</test>',
        $dom->saveXML($dom->documentElement)
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testWithNamespacesFromDocument() {
      $dom = new Document();
      $dom->registerNamespace('bar', 'urn:bar');
      $fragment = $dom->createDocumentFragment();
      $fragment->appendXml('<bar:test>success</bar:test>');
      $dom->appendChild($fragment);
      $this->assertEquals(
        '<bar:test xmlns:bar="urn:bar">success</bar:test>',
        $dom->saveXML($dom->documentElement)
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testWithDefaultNamespace() {
      $dom = new Document();
      $fragment = $dom->createDocumentFragment();
      $fragment->registerNamespace('', 'urn:bar');
      $fragment->appendXml('<test>success</test>');
      $dom->appendChild($fragment);
      $this->assertEquals(
        '<test xmlns="urn:bar">success</test>',
        $dom->saveXML($dom->documentElement)
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testWithNamespacesFromElementNode() {
      $dom = new Document();
      $dom->appendChild($dom->createElementNS('urn:bar', 'bar:root'));
      $fragment = $dom->createDocumentFragment();
      $fragment->namespaces($dom->documentElement);
      $fragment->appendXml('<bar:test>success</bar:test>');
      $dom->documentElement->appendChild($fragment);
      $this->assertEquals(
        '<bar:root xmlns:bar="urn:bar"><bar:test>success</bar:test></bar:root>',
        $dom->saveXML($dom->documentElement)
      );
    }

     /**
     * @covers FluentDOM\DocumentFragment
     */
    public function testWithNamespacesList() {
      $dom = new Document();
      $fragment = $dom->createDocumentFragment();
      $fragment->appendXml('<foo:test>success</foo:test>', ['foo' => 'urn:bar']);
      $dom->appendChild($fragment);
      $this->assertEquals(
        '<foo:test xmlns:foo="urn:bar">success</foo:test>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}