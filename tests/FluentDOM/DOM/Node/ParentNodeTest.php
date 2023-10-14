<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Node {

  use FluentDOM\DOM\Document;
  use FluentDOM\Exceptions\UndeclaredPropertyError;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  /**
   * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
   */
  class ParentNodeTest extends TestCase {

    public function testIssetFirstElementChildExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertTrue(isset($document->firstElementChild));
    }

    public function testIssetFirstElementChildExpectingFalse(): void {
      $document = new Document();
      $this->assertFalse(isset($document->firstElementChild));
    }

    public function testGetFirstElementChild(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertSame($document->documentElement, $document->firstElementChild);
    }

    public function testGetFirstElementChildOnFragment(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('TEXT<bar/>');
      $this->assertEquals(
        '<bar/>',
        $document->saveXML($fragment->firstElementChild)
      );
    }

    public function testGetFirstElementChildExpectingNull(): void {
      $document = new Document();
      $this->assertNull(
        $document->firstElementChild
      );
    }

    public function testGetFirstElementChildOnFragmentExpectingNull(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('TEXT');
      $this->assertNull(
        $fragment->firstElementChild
      );
    }

    public function testSetFirstElementChildExpectingException(): void {
      $document = new Document();
      $this->expectException(\Error::class);
      $document->firstElementChild = $document->createElement('dummy');
    }

    public function testIssetLastElementChildExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertTrue(isset($document->lastElementChild));
    }

    public function testIssetLastElementChildExpectingFalse(): void {
      $document = new Document();
      $this->assertFalse(isset($document->lastElementChild));
    }

    public function testGetLastElementChild(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $node = $document->lastElementChild;
      $this->assertSame(
        $document->documentElement,
        $node
      );
    }

    public function testGetLastElementChildOnFragment(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('TEXT<bar/><foobar/>TEXT');
      $this->assertEquals(
        '<foobar/>',
        $document->saveXML($fragment->lastElementChild)
      );
    }

    public function testGetLastElementChildExpectingNull(): void {
      $document = new Document();
      $this->assertNull(
        $document->lastElementChild
      );
    }

    public function testGetLastElementChildOnFragmentExpectingNull(): void {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('TEXT');
      $this->assertNull(
        $fragment->lastElementChild
      );
    }

    public function testSetLastElementChildExpectingException(): void {
      $document = new Document();
      $this->expectException(\Error::class);
      $document->lastElementChild = $document->createElement('dummy');
    }

    /**
     * @testWith
     *   [0, "<foo/>"]
     *   [1, "<foo><bar/></foo>"]
     *   [1, "<foo>text<bar/>text</foo>"]
     *   [1, "<foo>text<bar><child/></bar>text</foo>"]
     */
    public function testChildElementCount(int $expected, string $xml): void {
      $document = new Document();
      $document->loadXML($xml);
      $this->assertTrue(isset($document->documentElement->childElementCount));
      $this->assertSame($expected, $document->documentElement->childElementCount);
    }

    public function testChildElementCountOnDocument(): void {
      $document = new Document();
      $this->assertTrue(isset($document->childElementCount));
      $this->assertSame(0, $document->childElementCount);
      $document->loadXML('<foo/>');
      $this->assertSame(1, $document->childElementCount);
    }

    public function testPrepend(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document->documentElement->prepend('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED<bar/></foo>',
        $document->saveXML()
      );
    }

    public function testPrependToNodeWithoutChildren(): void {
      $document = new Document();
      $document->loadXML('<foo></foo>');
      $document->documentElement->prepend('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED</foo>',
        $document->saveXML()
      );
    }

    public function testAppend(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document->documentElement->append('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED</foo>',
        $document->saveXML()
      );
    }
  }
}
