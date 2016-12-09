<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class MutationMacroTest extends TestCase {

    /**
     * @covers \FluentDOM\Node\MutationMacro
     */
    public function testExpandFromElementNode() {
      $dom = new Document();
      $node = $dom->createElement('foo');
      $fragment = MutationMacro::expand($dom, $node);
      $this->assertInstanceOf(\DOMDocumentFragment::class, $fragment);
      $this->assertEquals(1, $fragment->childNodes->length);
    }

    /**
     * @covers \FluentDOM\Node\MutationMacro
     */
    public function testExpandFromString() {
      $dom = new Document();
      $fragment = MutationMacro::expand($dom, 'STRING');
      $this->assertInstanceOf(\DOMDocumentFragment::class, $fragment);
      $this->assertEquals(1, $fragment->childNodes->length);
    }

    /**
     * @covers \FluentDOM\Node\MutationMacro
     */
    public function testExpandFromArrayOfStrings() {
      $dom = new Document();
      $fragment = MutationMacro::expand($dom, ['STRING_ONE', 'STRING_TWO']);
      $this->assertInstanceOf(\DOMDocumentFragment::class, $fragment);
      $this->assertEquals(2, $fragment->childNodes->length);
    }

    /**
     * @covers \FluentDOM\Node\MutationMacro
     */
    public function testExpandFromInvalidArgumentExpectingException() {
      $dom = new Document();
      $this->expectException(\InvalidArgumentException::class);
      MutationMacro::expand($dom, [new \stdClass()]);
    }

    /**
     * @covers \FluentDOM\Node\MutationMacro
     */
    public function testExpandFromDocument() {
      $dom = new Document();
      $dom->loadXml('<one/>');
      $addDom = new Document();
      $addDom->loadXml('<two/>');
      $this->assertXmlStringEqualsXmlString(
        '<two/>',
        $dom->saveXML(MutationMacro::expand($dom, $addDom))
      );
    }

    /**
     * @covers \FluentDOM\Node\MutationMacro
     */
    public function testExpandFromNodeInOtherDocument() {
      $dom = new Document();
      $dom->loadXml('<one/>');
      $addDom = new Document();
      $addDom->loadXml('<two/>');
      $this->assertXmlStringEqualsXmlString(
        '<two/>',
        $dom->saveXML(MutationMacro::expand($dom, $addDom->documentElement))
      );
    }
  }
}