<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class XmlTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Xml
     */
    public function testSupportsExpectingTrue() {
      $loader = new Xml();
      $this->assertTrue($loader->supports('text/xml'));
    }

    /**
     * @covers FluentDOM\Loader\Xml
     */
    public function testSupportsExpectingFalse() {
      $loader = new Xml();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\Xml
     * @covers FluentDOM\Loader\Supports
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
     * @covers FluentDOM\Loader\Xml
     * @covers FluentDOM\Loader\Supports
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
     * @covers FluentDOM\Loader\Xml
     * @covers FluentDOM\Loader\Supports
     */
    public function testLoadWithValidXmlFile() {
      $loader = new Xml();
      $this->assertInstanceOf(
        'DOMDocument',
        $loader->load(
          __DIR__.'/TestData/loader.xml',
          'text/xml'
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\Xml
     */
    public function testLoadWithUnsupportType() {
      $loader = new Xml();
      $this->assertNull(
        $loader->load(
          __DIR__.'/TestData/loader.html',
          'text/html'
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\Xml
     * @covers FluentDOM\Loader\Supports
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
  }
}