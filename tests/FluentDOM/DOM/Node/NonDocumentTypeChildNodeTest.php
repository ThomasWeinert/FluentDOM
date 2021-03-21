<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Node {

  use FluentDOM\DOM\Document;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class NonDocumentTypeChildNodeTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testIssetNextElementSiblingExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo><!-- START -->TEXT<bar index="1"/></foo>');
      $this->assertTrue(isset($document->documentElement->firstChild->nextElementSibling));
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testIssetNextElementSiblingExpectingFalse(): void {
      $document = new Document();
      $document->loadXML('<foo><!-- START -->TEXT</foo>');
      $this->assertFalse(isset($document->documentElement->firstChild->nextElementSibling));
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testGetNextElementSibling(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT<bar index="1"/></foo>');
      $node = $document->documentElement->firstChild->nextElementSibling;
      $this->assertEquals(
        '<bar index="1"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testGetNextElementSiblingFromCommentNode(): void {
      $document = new Document();
      $document->loadXML('<foo><!-- START -->TEXT<bar index="1"/></foo>');
      $node = $document->documentElement->firstChild->nextElementSibling;
      $this->assertEquals(
        '<bar index="1"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testGetNextElementSiblingExpectingNull(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT</foo>');
      $this->assertNull(
        $document->documentElement->firstChild->nextElementSibling
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testIssetPreviousElementSiblingExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT<!-- START --></foo>');
      $this->assertTrue(isset($document->documentElement->lastChild->previousElementSibling));
    }
    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testIssetPreviousElementSiblingExpectingFalse(): void {
      $document = new Document();
      $document->loadXML('<foo>TEXT<!-- START --></foo>');
      $this->assertFalse(isset($document->documentElement->lastChild->previousElementSibling));
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testGetPreviousElementSibling(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT<bar index="1"/></foo>');
      $node = $document->documentElement->lastChild->previousElementSibling;
      $this->assertEquals(
        '<bar index="0"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testPreviousElementSiblingFromCommentNode(): void {
      $document = new Document();
      $document->loadXML('<foo><bar index="0"/>TEXT<!-- START --></foo>');
      $node = $document->documentElement->lastChild->previousElementSibling;
      $this->assertEquals(
        '<bar index="0"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testPreviousElementSiblingExpectingNull(): void {
      $document = new Document();
      $document->loadXML('<foo>TEXT<bar index="1"/></foo>');
      $this->assertNull(
        $document->documentElement->lastChild->previousElementSibling
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testGetParentPropertyFromCommentNode(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $this->assertEquals(
        'comment',
        $document->documentElement->firstChild->textContent
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testSetNextElementChildExpectingException(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $this->expectErrorMessage("Cannot write property");
      $document->documentElement->firstChild->nextElementSibling = $document->createElement('foo');
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testSetPreviousElementChildExpectingException(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $this->expectErrorMessage("Cannot write property");
      $document->documentElement->firstChild->previousElementSibling = $document->createElement('foo');
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testSetUnknownProperty(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $node = $document->documentElement->firstChild;
      $node->SOME_PROPERTY = 'success';
      $this->assertEquals('success', $node->SOME_PROPERTY);
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testUnsetUnknownProperty(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $node = $document->documentElement->firstChild;
      $this->expectPropertyIsUndefined();
      unset($node->SOME_PROPERTY);
      $this->assertNull($node->SOME_PROPERTY);
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testIssetUnknownPropertyExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $node = $document->documentElement->firstChild;
      $node->SOME_PROPERTY = 'foo';
      $this->assertTrue(isset($node->SOME_PROPERTY));
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testIssetUnknownPropertyExpectingFalse(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $node = $document->documentElement->firstChild;
      $this->assertFalse(isset($node->SOME_PROPERTY));
    }

    /**
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers \FluentDOM\DOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testGetUnknownPropertyExpectingError(): void {
      $document = new Document();
      $document->loadXML('<foo><!--comment--></foo>');
      $node = $document->documentElement->firstChild;
      $this->expectPropertyIsUndefined();
      $node->SOME_PROPERTY;
    }
  }
}
