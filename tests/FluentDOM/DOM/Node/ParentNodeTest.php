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

  class ParentNodeTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testIssetFirstElementChildExpectingTrue() {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertTrue(isset($document->firstElementChild));
    }
    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testIssetFirstElementChildExpectingFalse() {
      $document = new Document();
      $this->assertFalse(isset($document->firstElementChild));
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChild() {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertSame($document->documentElement, $document->firstElementChild);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildOnFragment() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('TEXT<bar/>');
      $this->assertEquals(
        '<bar/>',
        $document->saveXML($fragment->firstElementChild)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildExpectingNull() {
      $document = new Document();
      $this->assertNull(
        $document->firstElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildOnFragmentExpectingNull() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('TEXT');
      $this->assertNull(
        $fragment->firstElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testSetFirstElementChildExpectingException() {
      $document = new Document();
      $this->expectException(
        \BadMethodCallException::class
      );
      $document->firstElementChild = $document->createElement('dummy');
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testIssetLastElementChildExpectingTrue() {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertTrue(isset($document->lastElementChild));
    }
    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testIssetLastElementChildExpectingFalse() {
      $document = new Document();
      $this->assertFalse(isset($document->lastElementChild));
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChild() {
      $document = new Document();
      $document->loadXML('<foo/>');
      $node = $document->lastElementChild;
      $this->assertSame(
        $document->documentElement,
        $node
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChildOnFragment() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('TEXT<bar/><foobar/>TEXT');
      $this->assertEquals(
        '<foobar/>',
        $document->saveXML($fragment->lastElementChild)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChildExpectingNull() {
      $document = new Document();
      $this->assertNull(
        $document->lastElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChildOnFragmentExpectingNull() {
      $document = new Document();
      $fragment = $document->createDocumentFragment();
      $fragment->appendXml('TEXT');
      $this->assertNull(
        $fragment->lastElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testSetLastElementChildExpectingException() {
      $document = new Document();
      $this->expectException(
        \BadMethodCallException::class
      );
      $document->lastElementChild = $document->createElement('dummy');
    }

    /**
     * @param int $expected
     * @param string $xml
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     * @testWith
     *   [0, "<foo/>"]
     *   [1, "<foo><bar/></foo>"]
     *   [1, "<foo>text<bar/>text</foo>"]
     *   [1, "<foo>text<bar><child/></bar>text</foo>"]
     */
    public function testChildElementCount($expected, $xml) {
      $document = new Document();
      $document->loadXML($xml);
      $this->assertTrue(isset($document->documentElement->childElementCount));
      $this->assertSame($expected, $document->documentElement->childElementCount);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testChildElementCountOnDocument() {
      $document = new Document();
      $this->assertTrue(isset($document->childElementCount));
      $this->assertSame(0, $document->childElementCount);
      $document->loadXML('<foo/>');
      $this->assertSame(1, $document->childElementCount);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
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

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testSetUnknownProperty() {
      $document = new Document();
      $document->UNKNOWN_PROPERTY = 'FOO';
      $this->assertEquals('FOO', $document->UNKNOWN_PROPERTY);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     * @version
     */
    public function testGetUnknownProperty() {
      $document = new Document();
      if ((error_reporting() & E_NOTICE) === E_NOTICE) {
        $this->expectNotice();
      }
      $this->assertNull($document->UNKNOWN_PROPERTY);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testUnsetUnknownProperty() {
      $document = new Document();
      unset($document->UNKNOWN_PROPERTY);
      $this->assertFalse(isset($document->UNKNOWN_PROPERTY));
    }
  }
}
