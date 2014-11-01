<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class MutationMacroTest extends TestCase {

    /**
     * @covers FluentDOM\Node\MutationMacro
     */
    public function testExpandFromElementNode() {
      $dom = new Document();
      $node = $dom->createElement('foo');
      $fragment = MutationMacro::expand($dom, $node);
      $this->assertInstanceOf('DOMDocumentFragment', $fragment);
      $this->assertEquals(1, $fragment->childNodes->length);
    }

    /**
     * @covers FluentDOM\Node\MutationMacro
     */
    public function testExpandFromString() {
      $dom = new Document();
      $fragment = MutationMacro::expand($dom, 'STRING');
      $this->assertInstanceOf('DOMDocumentFragment', $fragment);
      $this->assertEquals(1, $fragment->childNodes->length);
    }

    /**
     * @covers FluentDOM\Node\MutationMacro
     */
    public function testExpandFromArrayOfStrings() {
      $dom = new Document();
      $fragment = MutationMacro::expand($dom, ['STRING_ONE', 'STRING_TWO']);
      $this->assertInstanceOf('DOMDocumentFragment', $fragment);
      $this->assertEquals(2, $fragment->childNodes->length);
    }
    /**
     * @covers FluentDOM\Node\MutationMacro
     */
    public function testExpandFromInvalidArgumentExpectingException() {
      $dom = new Document();
      $this->setExpectedException('InvalidArgumentException');
      MutationMacro::expand($dom, [new \stdClass()]);
    }
  }
}