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

  use FluentDOM\DOM\Comment;
  use FluentDOM\DOM\Document;
  use FluentDOM\Exceptions\UndeclaredPropertyError;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  /**
   * @coversNothing
   */
  class NonDocumentTypeChildNodeTest extends TestCase {

    public function testIssetNextElementSiblingExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo><!-- START -->TEXT<bar index="1"/></foo>');
      $this->assertTrue(isset($document->documentElement->firstChild->nextElementSibling));
    }

    public function testIssetNextElementSiblingExpectingFalse(): void {
      $document = new Document();
      $document->loadXML('<foo><!-- START -->TEXT</foo>');
      $this->assertFalse(isset($document->documentElement->firstChild->nextElementSibling));
    }

    public function testGetNextElementSibling(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT<bar index="1"/></foo>');
      $node = $document->documentElement->firstChild->nextElementSibling;
      $this->assertEquals(
        '<bar index="1"/>',
        $node->saveXml()
      );
    }

    public function testGetNextElementSiblingFromCommentNode(): void {
      $document = new Document();
      $document->loadXML('<foo><!-- START -->TEXT<bar index="1"/></foo>');
      $node = $document->documentElement->firstChild->nextElementSibling;
      $this->assertEquals(
        '<bar index="1"/>',
        $node->saveXml()
      );
    }

    public function testGetNextElementSiblingExpectingNull(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT</foo>');
      $this->assertNull(
        $document->documentElement->firstChild->nextElementSibling
      );
    }

    public function testIssetPreviousElementSiblingExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT<!-- START --></foo>');
      $this->assertTrue(isset($document->documentElement->lastChild->previousElementSibling));
    }

    public function testIssetPreviousElementSiblingExpectingFalse(): void {
      $document = new Document();
      $document->loadXML('<foo>TEXT<!-- START --></foo>');
      $this->assertFalse(isset($document->documentElement->lastChild->previousElementSibling));
    }

    public function testGetPreviousElementSibling(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT<bar index="1"/></foo>');
      $node = $document->documentElement->lastChild->previousElementSibling;
      $this->assertEquals(
        '<bar index="0"/>',
        $node->saveXml()
      );
    }

    public function testPreviousElementSiblingFromCommentNode(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT<!-- START --></foo>');
      $node = $document->documentElement->lastChild->previousElementSibling;
      $this->assertEquals(
        '<bar index="0"/>',
        $node->saveXml()
      );
    }

    public function testPreviousElementSiblingExpectingNull(): void {
      $document = new Document();
      $document->loadXML('<foo>TEXT<bar index="1"/></foo>');
      $this->assertNull(
        $document->documentElement->lastChild->previousElementSibling
      );
    }

    public function testGetParentPropertyFromCommentNode(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $this->assertEquals(
        'comment',
        $document->documentElement->firstChild->textContent
      );
    }

    public function testSetNextElementChildExpectingException(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $this->expectException(\Error::class);
      $document->documentElement->firstChild->nextElementSibling = $document->createElement('foo');
    }

    public function testSetPreviousElementChildExpectingException(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $this->expectException(\Error::class);
      $document->documentElement->firstChild->previousElementSibling = $document->createElement('foo');
    }

    public function testIssetUnknownPropertyExpectingFalse(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $node = $document->documentElement->firstChild;
      /** @noinspection MissingIssetImplementationInspection */
      $this->assertFalse(isset($node->SOME_PROPERTY));
    }

    public function testGetUnknownPropertyExpectingError(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $node = $document->documentElement->firstChild;
      $this->expectException(\ErrorException::class);
      /** @noinspection PhpExpressionResultUnusedInspection */
      /** @noinspection PhpUndefinedFieldInspection */
      $node->SOME_PROPERTY;
    }
  }
}
