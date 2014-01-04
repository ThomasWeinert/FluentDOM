<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class QueryTest extends TestCase {

    /**
     * @group Load
     * @covers FluentDOM\Query::load
     */
    public function testLoadWithDocument() {
      $fd = new Query();
      $fd->load($dom = new \DOMDocument());
      $this->assertSame(
        $dom,
        $fd->document
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Query::formatOutput
     */
    public function testFormatOutput() {
      $fd = new Query();
      $fd->document->loadXml('<html><body><br/></body></html>');
      $fd->formatOutput();
      $expected =
        "<?xml version=\"1.0\"?>\n".
        "<html>\n".
        "  <body>\n".
        "    <br/>\n".
        "  </body>\n".
        "</html>\n";
      $this->assertEquals('text/xml', $fd->contentType);
      $this->assertSame($expected, (string)$fd);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Query::formatOutput
     */
    public function testFormatOutputWithContentTypeHtml() {
      $fd = new Query();
      $fd->document->loadXml('<html><body><br/></body></html>');
      $fd->formatOutput('text/html');
      $expected = "<html><body><br></body></html>\n";
      $this->assertEquals('text/html', $fd->contentType);
      $this->assertSame($expected, (string)$fd);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Query::spawn
     */
    public function testSpawn() {
      $fdParent = new Query;
      $fdChild = $fdParent->spawn();
      $this->assertAttributeSame(
        $fdParent,
        '_parent',
        $fdChild
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Query::spawn
     */
    public function testSpawnWithElements() {
      $dom = new \DOMDocument;
      $node = $dom->createElement('test');
      $dom->appendChild($node);
      $fdParent = new Query();
      $fdParent->load($dom);
      $fdChild = $fdParent->spawn($node);
      $this->assertSame(
        array($node),
        iterator_to_array($fdChild)
      );
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Query::offsetExists
     *
     */
    public function testOffsetExistsExpectingTrue() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertTrue(isset($query[1]));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Query::offsetExists
     *
     */
    public function testOffsetExistsExpectingFalse() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertFalse(isset($query[99]));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Query::offsetGet
     */
    public function testOffsetGet() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertEquals('text2', $query[1]->nodeValue);
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Query::offsetGet
     */
    public function testOffsetSetExpectingException() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->setExpectedException('BadMethodCallException');
      $query[2] = '123';
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Query::offsetGet
     */
    public function testOffsetUnsetExpectingException() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->setExpectedException('BadMethodCallException');
      unset($query[2]);
    }

    /**
     * @group Interfaces
     * @group Countable
     * @covers FluentDOM\Query::count
     */
    public function testInterfaceCountableExpecting3() {
      $fd = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertCount(3, $fd);
    }

    /**
     * @group Interfaces
     * @group Countable
     * @covers FluentDOM\Query::count
     */
    public function testInterfaceCountableExpectingZero() {
      $fd = $this->getQueryFixtureFromString(self::XML, '//non-existing');
      $this->assertCount(0, $fd);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__isset
     * @covers FluentDOM\Query::__get
     * @covers FluentDOM\Query::__set
     */
    public function testDynamicProperty() {
      $fd = new Query();
      $this->assertEquals(FALSE, isset($fd->dynamicProperty));
      $this->assertEquals(NULL, $fd->dynamicProperty);
      $fd->dynamicProperty = 'test';
      $this->assertEquals(TRUE, isset($fd->dynamicProperty));
      $this->assertEquals('test', $fd->dynamicProperty);
    }

    /**
     * @covers FluentDOM\Query::__set
     */
    public function testSetPropertyXpath() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $this->setExpectedException('BadMethodCallException');
      $fd->xpath = $fd->xpath();
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__isset
     */
    public function testIssetPropertyLength() {
      $fd = new Query();
      $this->assertTrue(isset($fd->length));
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__get
     */
    public function testGetPropertyLength() {
      $fd = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertEquals(3, $fd->length);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__set
     */
    public function testSetPropertyLength() {
      $fd = new Query;
      $this->setExpectedException('BadMethodCallException');
      $fd->length = 50;
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__isset
     */
    public function testIssetPropertyContentType() {
      $fd = new Query();
      $this->assertTrue(isset($fd->contentType));
    }


    /**
     * @group Properties
     * @covers FluentDOM\Query::__get
     */
    public function testGetPropertyContentType() {
      $fd = new Query();
      $this->assertEquals('text/xml', $fd->contentType);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__set
     * @covers FluentDOM\Query::setContentType
     * @dataProvider getContentTypeSamples
     */
    public function testSetPropertyContentType($contentType, $expected) {
      $fd = new Query();
      $fd->contentType = $contentType;
      $this->assertAttributeEquals($expected, '_contentType', $fd);
    }

    public function getContentTypeSamples() {
      return array(
        array('text/xml', 'text/xml'),
        array('text/html', 'text/html'),
        array('xml', 'text/xml'),
        array('html', 'text/html'),
        array('TEXT/XML', 'text/xml'),
        array('TEXT/HTML', 'text/html'),
        array('XML', 'text/xml'),
        array('HTML', 'text/html')
      );
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__set
     * @covers FluentDOM\Query::setContentType
     */
    public function testSetPropertyContentTypeChaining() {
      $fdParent = new Query();
      $fdChild = $fdParent->spawn();
      $fdChild->contentType = 'text/html';
      $this->assertEquals(
        'text/html',
        $fdParent->contentType
      );
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__set
     * @covers FluentDOM\Query::setContentType
     */
    public function testSetPropertyContentTypeInvalid() {
      $fd = new Query();
      $this->setExpectedException('UnexpectedValueException');
      $fd->contentType = 'Invalid Type';
    }

    /**
     * @group MagicFunctions
     * @group StringCastable
     * @covers FluentDOM\Query::__toString
     */
    public function testMagicToString() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $this->assertEquals($fd->document->saveXML(), (string)$fd);
    }

    /**
     * @group MagicFunctions
     * @group StringCastable
     * @covers FluentDOM\Query::__toString
     */
    public function testMagicToStringHtml() {
      $dom = new \DOMDocument();
      $dom->loadHTML(self::HTML);
      $fd = new Query();
      $fd = $fd->load($dom);
      $fd->contentType = 'html';
      $this->assertEquals($dom->saveHTML(), (string)$fd);
    }
  }
}