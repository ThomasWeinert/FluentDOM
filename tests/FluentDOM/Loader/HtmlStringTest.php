<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class LoaderHtmlStringTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\HtmlString
     */
    public function testSupportsExpectingTrue() {
      $loader = new HtmlString();
      $this->assertTrue($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\HtmlString
     */
    public function testSupportsExpectingFalse() {
      $loader = new HtmlString();
      $this->assertFalse($loader->supports('text/xml'));
    }

    /**
     * @covers FluentDOM\Loader\HtmlString
     */
    public function testLoadWithValidXml() {
      $loader = new HtmlString();
      $this->assertInstanceOf('DOMDocument', $loader->load('<html/>'));
    }

    /**
     * @covers FluentDOM\Loader\HtmlString
     */
    public function testLoadWithInvalidXml() {
      $loader = new HtmlString();
      $this->assertNull($loader->load('no xml'));
    }
  }
}