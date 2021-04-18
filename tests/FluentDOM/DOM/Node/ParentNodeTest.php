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
    public function testIssetFirstElementChildExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertTrue(isset($document->firstElementChild));
    }
    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testIssetFirstElementChildExpectingFalse(): void {
      $document = new Document();
      $this->assertFalse(isset($document->firstElementChild));
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChild(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertSame($document->documentElement, $document->firstElementChild);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildOnFragment(): void {
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
    public function testGetFirstElementChildExpectingNull(): void {
      $document = new Document();
      $this->assertNull(
        $document->firstElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetFirstElementChildOnFragmentExpectingNull(): void {
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
    public function testSetFirstElementChildExpectingException(): void {
      $document = new Document();
      $this->expectErrorMessage("Cannot write property");
      $document->firstElementChild = $document->createElement('dummy');
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testIssetLastElementChildExpectingTrue(): void {
      $document = new Document();
      $document->loadXML('<foo/>');
      $this->assertTrue(isset($document->lastElementChild));
    }
    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testIssetLastElementChildExpectingFalse(): void {
      $document = new Document();
      $this->assertFalse(isset($document->lastElementChild));
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChild(): void {
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
    public function testGetLastElementChildOnFragment(): void {
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
    public function testGetLastElementChildExpectingNull(): void {
      $document = new Document();
      $this->assertNull(
        $document->lastElementChild
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testGetLastElementChildOnFragmentExpectingNull(): void {
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
    public function testSetLastElementChildExpectingException(): void {
      $document = new Document();
      $this->expectErrorMessage("Cannot write property");
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
    public function testChildElementCount(int $expected, string $xml): void {
      $document = new Document();
      $document->loadXML($xml);
      $this->assertTrue(isset($document->documentElement->childElementCount));
      $this->assertSame($expected, $document->documentElement->childElementCount);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testChildElementCountOnDocument(): void {
      $document = new Document();
      $this->assertTrue(isset($document->childElementCount));
      $this->assertSame(0, $document->childElementCount);
      $document->loadXML('<foo/>');
      $this->assertSame(1, $document->childElementCount);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     */
    public function testPrepend(): void {
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
    public function testPrependToNodeWithoutChildren(): void {
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
    public function testAppend(): void {
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
    public function testSetUnknownProperty(): void {
      $document = new Document();
      $document->UNKNOWN_PROPERTY = 'FOO';
      $this->assertEquals('FOO', $document->UNKNOWN_PROPERTY);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     * @version
     */
    public function testGetUnknownProperty(): void {
      $document = new Document();
      if ((error_reporting() & E_NOTICE) === E_NOTICE) {
        if (PHP_VERSION_ID < 80000) {
          $this->expectNotice();
        } else {
          $this->expectWarning();
        }
      }
      $this->assertNull($document->UNKNOWN_PROPERTY);
    }

    /**
     * @covers \FluentDOM\DOM\Node\ParentNode\Implementation
     * @covers \FluentDOM\DOM\Node\ParentNode\Properties
     */
    public function testUnsetUnknownProperty(): void {
      $document = new Document();
      unset($document->UNKNOWN_PROPERTY);
      $this->assertFalse(isset($document->UNKNOWN_PROPERTY));
    }
  }
}
