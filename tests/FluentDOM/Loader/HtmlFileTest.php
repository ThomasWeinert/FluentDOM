<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class LoaderHtmlFileTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\HtmlFile
     */
    public function testSupportsExpectingTrue() {
      $loader = new HtmlFile();
      $this->assertTrue($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\HtmlFile
     */
    public function testSupportsExpectingFalse() {
      $loader = new HtmlFile();
      $this->assertFalse($loader->supports('text/xtml'));
    }

    /**
     * @covers FluentDOM\Loader\HtmlFile
     */
    public function testLoadWithValidXml() {
      $loader = new HtmlFile();
      $this->assertInstanceOf('DOMDocument', $loader->load('data://text/plain,'.urlencode('<html/>')));
    }

    /**
     * @covers FluentDOM\Loader\HtmlFile
     */
    public function testLoadWithInvalidXml() {
      $loader = new HtmlFile();
      $this->assertNull($loader->load('<node/>'));
    }
  }
}