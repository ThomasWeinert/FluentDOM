<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class HtmlTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Html
     */
    public function testSupportsExpectingTrue() {
      $loader = new Html();
      $this->assertTrue($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\Html
     * @covers FluentDOM\Loader\Supports
     */
    public function testSupportsExpectingFalse() {
      $loader = new Html();
      $this->assertFalse($loader->supports('text/xml'));
    }

    /**
     * @covers FluentDOM\Loader\Html
     * @covers FluentDOM\Loader\Supports
     */
    public function testLoadWithValidXml() {
      $loader = new Html();
      $this->assertInstanceOf(
        'DOMDocument',
        $loader->load(
          '<html/>',
          'text/html'
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\Html
     */
    public function testLoadWithValidXmlFile() {
      $loader = new Html();
      $this->assertInstanceOf(
        'DOMDocument',
        $loader->load(
          __DIR__.'/TestData/loader.html',
          'text/html'
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\Html
     */
    public function testLoadWithUnsupportedType() {
      $loader = new Html();
      $this->assertNull(
        $loader->load(
          __DIR__.'/TestData/loader.xml',
          'text/xml'
        )
      );
    }
  }
}