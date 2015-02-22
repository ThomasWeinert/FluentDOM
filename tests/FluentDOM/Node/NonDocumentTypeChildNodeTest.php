<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class NonDocumentTypeChildNodeTest extends TestCase {

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testNextElementSibling() {
      $dom = new Document();
      $dom->loadXML('<foo><bar index="0"/>TEXT<bar index="1"/></foo>');
      $node = $dom->documentElement->firstChild->nextElementSibling;
      $this->assertEquals(
        '<bar index="1"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testNextElementSiblingFromCommentNode() {
      $dom = new Document();
      $dom->loadXML('<foo><!-- START -->TEXT<bar index="1"/></foo>');
      $node = $dom->documentElement->firstChild->nextElementSibling;
      $this->assertEquals(
        '<bar index="1"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testNextElementSiblingExpectingNull() {
      $dom = new Document();
      $dom->loadXML('<foo><bar index="0"/>TEXT</foo>');
      $this->assertNull(
        $dom->documentElement->firstChild->nextElementSibling
      );
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testPreviousElementSibling() {
      $dom = new Document();
      $dom->loadXML('<foo><bar index="0"/>TEXT<bar index="1"/></foo>');
      $node = $dom->documentElement->lastChild->previousElementSibling;
      $this->assertEquals(
        '<bar index="0"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testPreviousElementSiblingFromCommentNode() {
      $dom = new Document();
      $dom->loadXML('<foo><bar index="0"/>TEXT<!-- START --></foo>');
      $node = $dom->documentElement->lastChild->previousElementSibling;
      $this->assertEquals(
        '<bar index="0"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testPreviousElementSiblingExpectingNull() {
      $dom = new Document();
      $dom->loadXML('<foo>TEXT<bar index="1"/></foo>');
      $this->assertNull(
        $dom->documentElement->lastChild->previousElementSibling
      );
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testGetParentPropertyFromCommentNode() {
      $dom = new Document();
      $dom->loadXML('<foo><!--comment--></foo>');
      $this->assertEquals(
        'comment',
        $dom->documentElement->firstChild->textContent
      );
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testSetNextElementChildExpectingException() {
      $dom = new Document();
      $dom->loadXML('<foo><!--comment--></foo>');
      $this->setExpectedException('BadMethodCallException');
      $dom->documentElement->firstChild->nextElementSibling = $dom->createElement('foo');
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testSetPreviousElementChildExpectingException() {
      $dom = new Document();
      $dom->loadXML('<foo><!--comment--></foo>');
      $this->setExpectedException('BadMethodCallException');
      $dom->documentElement->firstChild->previousElementSibling = $dom->createElement('foo');
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Properties
     */
    public function testSetUnknownProperty() {
      $dom = new Document();
      $dom->loadXML('<foo><!--comment--></foo>');
      $dom->documentElement->firstChild->UNKNOWN_PROPERTY = 'success';
      $this->setExpectedException(
        'PHPUnit_Framework_Error_Notice',
        'Undefined property: FluentDOM\\Comment::$UNKNOWN_PROPERTY'
      );
      $dom->documentElement->firstChild->UNKNOWN_PROPERTY;
    }
  }
}