<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class LoaderXmlFileTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\XmlFile
     */
    public function testSupportsExpectingTrue() {
      $loader = new XmlFile();
      $this->assertTrue($loader->supports('text/xml'));
    }

    /**
     * @covers FluentDOM\Loader\XmlFile
     */
    public function testSupportsExpectingFalse() {
      $loader = new XmlFile();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\XmlFile
     */
    public function testLoadWithValidXml() {
      $loader = new XmlFile();
      $this->assertInstanceOf(
        'DOMDocument',
        $loader->load(
          'data://text/plain,'.urlencode('<xml/>'),
          'text/xml'
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\XmlFile
     */
    public function testLoadWithInvalidXml() {
      $loader = new XmlFile();
      $this->assertNull(
        $loader->load(
          '<node/>',
          'text/xml'
        )
      );
    }
  }
}