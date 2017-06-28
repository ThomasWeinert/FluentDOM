<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class ChildNodeTest extends TestCase {

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testRemoveWithElementNode() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $dom('/foo/bar')->item(0)->remove();
      $this->assertXmlStringEqualsXmlString(
        '<foo/>',
        $dom->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testBeforeInsertStringAsTextNodeBeforeElementNode() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $dom('/foo/bar')->item(0)->before('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED<bar/></foo>',
        $dom->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testAfterAppendsStringAsTextNode() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $dom('/foo/bar')->item(0)->after('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED</foo>',
        $dom->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testAfterAppendsStringAsTextNodeBeforeElementNode() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/><bar/></foo>');
      $dom('/foo/bar')->item(0)->after('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED<bar/></foo>',
        $dom->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testAfterAppendsCommentAfterDocumentElement() {
      $document = new Document();
      $document->loadXML('<foo/>');
      $document('/foo')->item(0)->after($document->createComment('ABC'));
      $this->assertEquals(
        "<?xml version=\"1.0\"?>\n<foo/>\n<!--ABC-->\n",
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testReplaceWithElementNode() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $newNode = $dom->createElement('replaced');
      $dom('/foo/bar')->item(0)->replace($newNode);
      $this->assertXmlStringEqualsXmlString(
        '<foo><replaced/></foo>',
        $dom->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testReplaceWithDocumentElement() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $newNode = $dom->createElement('replaced');
      $dom('/foo')->item(0)->replace($newNode);
      $this->assertXmlStringEqualsXmlString(
        '<replaced/>',
        $dom->saveXML()
      );
    }
  }
}