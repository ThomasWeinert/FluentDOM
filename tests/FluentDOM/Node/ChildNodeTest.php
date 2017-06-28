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
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document('/foo/bar')->item(0)->remove();
      $this->assertXmlStringEqualsXmlString(
        '<foo/>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testBeforeInsertStringAsTextNodeBeforeElementNode() {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document('/foo/bar')->item(0)->before('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED<bar/></foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testAfterAppendsStringAsTextNode() {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document('/foo/bar')->item(0)->after('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED</foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testAfterAppendsStringAsTextNodeBeforeElementNode() {
      $document = new Document();
      $document->loadXML('<foo><bar/><bar/></foo>');
      $document('/foo/bar')->item(0)->after('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED<bar/></foo>',
        $document->saveXML()
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
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $newNode = $document->createElement('replaced');
      $document('/foo/bar')->item(0)->replace($newNode);
      $this->assertXmlStringEqualsXmlString(
        '<foo><replaced/></foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Node\ChildNode\Implementation
     */
    public function testReplaceWithDocumentElement() {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $newNode = $document->createElement('replaced');
      $document('/foo')->item(0)->replace($newNode);
      $this->assertXmlStringEqualsXmlString(
        '<replaced/>',
        $document->saveXML()
      );
    }
  }
}