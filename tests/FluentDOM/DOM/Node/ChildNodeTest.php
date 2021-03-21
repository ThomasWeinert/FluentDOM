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

  class ChildNodeTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Node\ChildNode\Implementation
     */
    public function testRemoveWithElementNode(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document('/foo/bar')->item(0)->remove();
      $this->assertXmlStringEqualsXmlString(
        '<foo/>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ChildNode\Implementation
     */
    public function testBeforeInsertStringAsTextNodeBeforeElementNode(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document('/foo/bar')->item(0)->before('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED<bar/></foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ChildNode\Implementation
     */
    public function testAfterAppendsStringAsTextNode(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $document('/foo/bar')->item(0)->after('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED</foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ChildNode\Implementation
     */
    public function testAfterAppendsStringAsTextNodeBeforeElementNode(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/><bar/></foo>');
      $document('/foo/bar')->item(0)->after('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED<bar/></foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ChildNode\Implementation
     */
    public function testAfterAppendsCommentAfterDocumentElement(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $document('/foo')->item(0)->after($document->createComment('ABC'));
      $this->assertEquals(
        "<?xml version=\"1.0\"?>\n<foo/>\n<!--ABC-->\n",
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ChildNode\Implementation
     */
    public function testReplaceWithElementNode(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $newNode = $document->createElement('replaced');
      $document('/foo/bar')->item(0)->replaceWith($newNode);
      $this->assertXmlStringEqualsXmlString(
        '<foo><replaced/></foo>',
        $document->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ChildNode\Implementation
     */
    public function testReplaceWithDocumentElement(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $newNode = $document->createElement('replaced');
      $document('/foo')->item(0)->replaceWith($newNode);
      $this->assertXmlStringEqualsXmlString(
        '<replaced/>',
        $document->saveXML()
      );
    }
    /**
     * @covers \FluentDOM\DOM\Node\ChildNode\Implementation
     * @deprecated
     */
    public function testReplaceElementNode(): void {
      $document = new Document();
      $document->loadXML('<foo><bar/></foo>');
      $newNode = $document->createElement('replaced');
      $document('/foo/bar')->item(0)->replace($newNode);
      $this->assertXmlStringEqualsXmlString(
        '<foo><replaced/></foo>',
        $document->saveXML()
      );
    }
  }
}
