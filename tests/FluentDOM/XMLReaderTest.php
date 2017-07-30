<?php
namespace FluentDOM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;

  require_once __DIR__.'/TestCase.php';

  class XMLReaderTest extends TestCase {

    /**
     * @covers \FluentDOM\XMLReader
     */
    public function testTraverseSiblingsWithRegisteredNamespace() {
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
    public function testTraverseSiblingsWithNamespace() {
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
    public function testTraverseSiblingsWithFilter() {
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
    public function testTraverseSiblingsWithoutNamespace() {
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
    public function testTraverseAllSiblings() {
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
    public function testTraverseDescendantsWithRegisteredNamespace() {
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
    public function testTraverseDescendantsWithNamespace() {
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
    public function testTraverseDescendantsWithFilterFunctionAndTagName() {
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
    public function testTraverseDescendantsWithFilterFunction() {
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
    public function testTraverseDescendantsWithoutNamespace() {
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
    public function testGetAttributeWithNamespace() {
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
    public function testExpandToFluentDOMDocumentIncludingNamespaces() {
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
    public function testExpandToProvidedDocument() {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/TestData/xmlreader-1.xml');
      $reader->read();
      $document = new Document();
      $node = $reader->expand($document);
      $this->assertInstanceOf(Element::class, $node);
      $this->assertSame($document, $node->ownerDocument);
    }
  }
}



