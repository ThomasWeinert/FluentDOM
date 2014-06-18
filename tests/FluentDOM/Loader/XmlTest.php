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
      $this->assertInstanceOf(
        'DOMDocument',
        $loader->load(
          '<xml/>',
          'text/xml'
        )
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
  }
}