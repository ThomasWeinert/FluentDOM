<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class ParentNodeTest extends TestCase {

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChild() {
      $dom = new Document();
      $dom->loadXML('<foo>TEXT<bar/><foobar/></foo>');
      $node = $dom->documentElement->firstElementChild;
      $this->assertXmlStringEqualsXmlString(
        '<bar/>',
        $dom->saveXML($node)
      );
    }

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildOnDocument() {
      $dom = new Document();
      $dom->loadXML('<foo/>');
      $this->assertSame(
        $dom->documentElement,
        $dom->firstElementChild
      );
    }

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildExpectingNull() {
      $dom = new Document();
      $dom->loadXML('<foo>TEXT</foo>');
      $this->assertNull(
        $dom->documentElement->firstElementChild
      );
    }
    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChild() {
      $dom = new Document();
      $dom->loadXML('<foo>TEXT<bar/><foobar/>TEXT</foo>');
      $node = $dom->documentElement->lastElementChild;
      $this->assertXmlStringEqualsXmlString(
        '<foobar/>',
        $dom->saveXML($node)
      );
    }

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChildOnDocument() {
      $dom = new Document();
      $dom->loadXML('<foo/>');
      $this->assertSame(
        $dom->documentElement,
        $dom->lastElementChild
      );
    }

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChildExpectingNull() {
      $dom = new Document();
      $dom->loadXML('<foo>TEXT</foo>');
      $this->assertNull(
        $dom->documentElement->lastElementChild
      );
    }

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testPrepend() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $dom->documentElement->prepend('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED<bar/></foo>',
        $dom->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testPrependToNodeWithoutChildren() {
      $dom = new Document();
      $dom->loadXML('<foo></foo>');
      $dom->documentElement->prepend('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED</foo>',
        $dom->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     * @covers FluentDOM\Node\ParentNode\Properties
     */
    public function testAppend() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $dom->documentElement->append('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED</foo>',
        $dom->saveXML()
      );
    }
  }
}