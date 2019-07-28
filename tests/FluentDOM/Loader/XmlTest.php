<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\TestCase;
  use FluentDOM\Exceptions\InvalidSource;

  require_once __DIR__.'/../TestCase.php';

  class XmlTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Xml
     */
    public function testSupportsExpectingTrue() {
      $loader = new Xml();
      $this->assertTrue($loader->supports('text/xml'));
    }

    /**
     * @covers \FluentDOM\Loader\Xml
     */
    public function testSupportsExpectingFalse() {
      $loader = new Xml();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers \FluentDOM\Loader\Xml
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithValidXml() {
      $loader = new Xml();
      $document = $loader->load(
        '<xml><![CDATA[Test]]></xml>',
        'text/xml'
      );
      $this->assertEquals(
        '<xml><![CDATA[Test]]></xml>',
        $document->documentElement->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Xml
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadReplacingCdataInXml() {
      $loader = new Xml();
      $document = $loader->load(
        '<xml><![CDATA[Test]]></xml>',
        'text/xml',
        [
          Xml::LIBXML_OPTIONS => LIBXML_NOCDATA
        ]
      );
      $this->assertEquals(
        '<xml>Test</xml>',
        $document->documentElement->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Xml
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithValidXmlFileAllowFile() {
      $loader = new Xml();
      $this->assertInstanceOf(
        'DOMDocument',
        $loader->load(
          __DIR__.'/TestData/loader.xml',
          'text/xml',
          [
            Options::ALLOW_FILE => TRUE
          ]
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\Xml
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithValidXmlFileForceFile() {
      $loader = new Xml();
      $this->assertInstanceOf(
        'DOMDocument',
        $loader->load(
          __DIR__.'/TestData/loader.xml',
          'text/xml',
          [
            Options::IS_FILE => TRUE
          ]
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\Html
     */
    public function testLoadWithFileExpectingException() {
      $loader = new Xml();
      $this->expectException(InvalidSource\TypeFile::class);
      $loader->load(
        __DIR__.'/TestData/loader.xml',
        'text/xml'
      );
    }

    /**
     * @covers \FluentDOM\Loader\Xml
     */
    public function testLoadWithUnsupportedType() {
      $loader = new Xml();
      $this->assertNull(
        $loader->load(
          __DIR__.'/TestData/loader.html',
          'text/html'
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\Xml
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadFragmentWithValidXml() {
      $loader = new Xml();
      $fragment = $loader->loadFragment(
        'TEXT<xml><![CDATA[Test]]></xml>',
        'text/xml'
      );
      $this->assertEquals(
        'TEXT<xml><![CDATA[Test]]></xml>',
        $fragment->ownerDocument->saveXml($fragment)
      );
    }

    /**
     * @covers \FluentDOM\Loader\Xml
     */
    public function testLoadFragmentWithUnsupportedType() {
      $loader = new Xml();
      $this->assertNull(
        $loader->loadFragment(
          '',
          'text/html'
        )
      );
    }

    public function testLoadWithInvalidXmlExpectingException() {
      $loader = new Xml();
      $this->expectException(
        LoadingError\Libxml::class,
        'Libxml fatal error in line 1 at character 12: Premature end of data in tag foo line 1.'
      );
      $loader->load('<foo><bar/>', 'text/xml');
    }

    public function testLoadWithPreserveWhitespaceTrue() {
      $loader = new Xml();
      $document = $loader->load('<foo> <bar/> </foo>', 'xml', [Options::PRESERVE_WHITESPACE => TRUE]);
      $this->assertCount(3, $document->documentElement->childNodes);
    }

    public function testLoadWithPreserveWhitespaceFalse() {
      $loader = new Xml();
      $document = $loader->load('<foo> <bar/> </foo>', 'xml', [Options::PRESERVE_WHITESPACE => FALSE]);
      $this->assertCount(1, $document->documentElement->childNodes);
    }
  }
}
