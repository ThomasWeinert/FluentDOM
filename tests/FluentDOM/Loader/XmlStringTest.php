<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class LoaderTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\XmlString
     */
    public function testSupportsExpectingTrue() {
      $loader = new XmlString();
      $this->assertTrue($loader->supports('text/xml'));
    }

    /**
     * @covers FluentDOM\Loader\XmlString
     */
    public function testSupportsExpectingFalse() {
      $loader = new XmlString();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\XmlString
     */
    public function testLoadWithValidXml() {
      $loader = new XmlString();
      $this->assertInstanceOf('DOMDocument', $loader->load('<xml/>'));
    }

    /**
     * @covers FluentDOM\Loader\XmlString
     */
    public function testLoadWithInvalidXml() {
      $loader = new XmlString();
      $this->assertNull($loader->load('no xml'));
    }
  }
}