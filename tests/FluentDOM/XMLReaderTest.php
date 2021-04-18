<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\Exceptions\InvalidArgument;

  require_once __DIR__.'/TestCase.php';

  class XMLReaderTest extends TestCase {

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseSiblingsWithRegisteredNamespace(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');
      $reader->registerNamespace('foo', 'urn:foo');

      $result = [];
      $found = $reader->read('foo:child');
      while ($found) {
        $result[] = $reader->getAttribute('name');
        $found = $reader->next('foo:child');
      }

      $this->assertEquals(
        ['one', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseSiblingsWithNamespace(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');

      $result = [];
      $found = $reader->read('child', 'urn:foo');
      while ($found) {
        $result[] = $reader->getAttribute('name');
        $found = $reader->next('child', 'urn:foo');
      }

      $this->assertEquals(
        ['one', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseSiblingsWithFilter(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');

      $result = [];
      $filter = function(XMLReader $reader) {
        return $reader->localName == "child" && $reader->namespaceURI == 'urn:foo';
      };
      $found = $reader->read(NULL, NULL, $filter);
      while ($found) {
        $result[] = $reader->getAttribute('name');
        $found = $reader->next(NULL, NULL, $filter);
      }

      $this->assertEquals(
        ['one', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseSiblingsWithoutNamespace(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');

      $result = [];
      $found = $reader->read('child');
      while ($found) {
        $result[] = $reader->getAttribute('name');
        $found = $reader->next('child');
      }

      $this->assertEquals(
        ['one', 'two', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseAllSiblings(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');

      $result = [];
      $found = $reader->read('child');
      while ($found) {
        if ($reader->nodeType === XML_ELEMENT_NODE) {
          $result[] = $reader->getAttribute('name');
        }
        $found = $reader->next();
      }

      $this->assertEquals(
        ['one', 'two', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseDescendantsWithRegisteredNamespace(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');
      $reader->registerNamespace('foo', 'urn:foo');

      $result = [];
      while ($reader->read('foo:child')) {
        $result[] = $reader->getAttribute('name');
      }

      $this->assertEquals(
        ['one', 'one.one', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseDescendantsWithNamespace(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');

      $result = [];
      while ($reader->read('child', 'urn:foo')) {
        $result[] = $reader->getAttribute('name');
      }

      $this->assertEquals(
        ['one', 'one.one', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseDescendantsWithFilterFunctionAndTagName(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');

      $result = [];
      $filter = function(XMLReader $reader) { return $reader->namespaceURI === 'urn:foo'; };
      while ($reader->read('child', NULL, $filter)) {
        $result[] = $reader->getAttribute('name');
      }

      $this->assertEquals(
        ['one', 'one.one', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseDescendantsWithFilterFunction(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');

      $result = [];
      $filter = function(XMLReader $reader) {
        return
          $reader->nodeType !== XMLReader::END_ELEMENT &&
          $reader->localName === 'child' &&
          $reader->namespaceURI === 'urn:foo';
      };
      while ($reader->read(NULL, NULL, $filter)) {
        $result[] = $reader->getAttribute('name');
      }

      $this->assertEquals(
        ['one', 'one.one', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseDescendantsWithoutNamespace(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');

      $result = [];
      while ($reader->read('child')) {
        $result[] = $reader->getAttribute('name');
      }

      $this->assertEquals(
        ['one', 'one.one', 'two', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testGetAttributeWithNamespace(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');
      $reader->registerNamespace('b', 'urn:bar');
      $reader->read();
      $this->assertEquals(
        'value',
        $reader->getAttribute('b:attribute')
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testExpandToFluentDOMDocumentIncludingNamespaces(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');
      $reader->registerNamespace('foo', 'urn:foo');
      $reader->read();
      $node = $reader->expand();
      /** @var Document $document */
      $document = $node->ownerDocument;
      $this->assertInstanceOf(Element::class, $node);
      $this->assertEquals(
        ['foo' => 'urn:foo'],
        iterator_to_array($document->namespaces())
      );
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testExpandToProvidedDocument(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');
      $reader->read();
      $document = new Document();
      $node = $reader->expand($document);
      $this->assertInstanceOf(Element::class, $node);
      $this->assertSame($document, $node->ownerDocument);
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testAttachStream(): void {
      $fh = fopen(__DIR__.'/TestData/xmlreader-1.xml', 'rb');
      $reader = new XMLReader();
      $reader->attachStream($fh);
      $reader->read();
      $this->assertEquals('root', $reader->localName);
      fclose($fh);
    }

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testAttachStreamExpectingException(): void {
      $reader = new XMLReader();
      $this->expectException(InvalidArgument::class);
      /** @noinspection PhpParamsInspection */
      $reader->attachStream('dummy');
    }
  }
}



