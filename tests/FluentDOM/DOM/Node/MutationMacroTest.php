<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Document;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class MutationMacroTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Node\MutationMacro
     */
    public function testExpandFromElementNode(): void {
      $document = new Document();
      $node = $document->createElement('foo');
      $fragment = MutationMacro::expand($document, $node);
      $this->assertInstanceOf(\DOMDocumentFragment::class, $fragment);
      $this->assertEquals(1, $fragment->childNodes->length);
    }

    /**
     * @covers \FluentDOM\DOM\Node\MutationMacro
     */
    public function testExpandFromString(): void {
      $document = new Document();
      $fragment = MutationMacro::expand($document, 'STRING');
      $this->assertInstanceOf(\DOMDocumentFragment::class, $fragment);
      $this->assertEquals(1, $fragment->childNodes->length);
    }

    /**
     * @covers \FluentDOM\DOM\Node\MutationMacro
     */
    public function testExpandFromArrayOfStrings(): void {
      $document = new Document();
      $fragment = MutationMacro::expand($document, ['STRING_ONE', 'STRING_TWO']);
      $this->assertInstanceOf(\DOMDocumentFragment::class, $fragment);
      $this->assertEquals(2, $fragment->childNodes->length);
    }

    /**
     * @covers \FluentDOM\DOM\Node\MutationMacro
     */
    public function testExpandFromInvalidArgumentExpectingException(): void {
      $document = new Document();
      $this->expectException(\InvalidArgumentException::class);
      MutationMacro::expand($document, [new \stdClass()]);
    }

    /**
     * @covers \FluentDOM\DOM\Node\MutationMacro
     */
    public function testExpandFromDocument(): void {
      $document = new Document();
      $document->loadXml('<one/>');
      $addDom = new Document();
      $addDom->loadXml('<two/>');
      $this->assertXmlStringEqualsXmlString(
        '<two/>',
        $document->saveXML(MutationMacro::expand($document, $addDom))
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\MutationMacro
     */
    public function testExpandFromNodeInOtherDocument(): void {
      $document = new Document();
      $document->loadXml('<one/>');
      $addDom = new Document();
      $addDom->loadXml('<two/>');
      $this->assertXmlStringEqualsXmlString(
        '<two/>',
        $document->saveXML(MutationMacro::expand($document, $addDom->documentElement))
      );
    }
  }
}
