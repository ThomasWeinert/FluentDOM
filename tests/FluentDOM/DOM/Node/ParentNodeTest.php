<?php

namespace FluentDOM\Node {

  use FluentDOM\DOM\Document;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class ParentNodeTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChild() {
      $document = new Document();
      $document->loadXML('<foo>TEXT<bar/><foobar/></foo>');
      $node = $document->documentElement->firstElementChild;
      $this->assertXmlStringEqualsXmlString(
        '<bar/>',
        $document->saveXML($node)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildOnDocument() {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertSame(
        $document->documentElement,
        $document->firstElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildExpectingNull() {
      $document = new Document();
      $document->loadXML('<foo>TEXT</foo>');
      $this->assertNull(
        $document->documentElement->firstElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testSetFirstElementChildExpectingException() {
      $document = new Document();
      $document->loadXML('<foo>TEXT</foo>');
      $this->expectException(
        \BadMethodCallException::class
      );
      $document->firstElementChild = $document->createElement('dummy');
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChild() {
      $document = new Document();
      $document->loadXML('<foo>TEXT<bar/><foobar/>TEXT</foo>');
      $node = $document->documentElement->lastElementChild;
      $this->assertXmlStringEqualsXmlString(
        '<foobar/>',
        $document->saveXML($node)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChildOnDocument() {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertSame(
        $document->documentElement,
        $document->lastElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChildExpectingNull() {
      $document = new Document();
      $document->loadXML('<foo>TEXT</foo>');
      $this->assertNull(
        $document->documentElement->lastElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testSetLastElementChildExpectingException() {
      $document = new Document();
      $document->loadXML('<foo>TEXT</foo>');
      $this->expectException(
        \BadMethodCallException::class
      );
      $document->lastElementChild = $document->createElement('dummy');
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testPrepend() {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document->documentElement->prepend('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED<bar/></foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testPrependToNodeWithoutChildren() {
      $document = new Document();
      $document->loadXML('<foo></foo>');
      $document->documentElement->prepend('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED</foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testAppend() {
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