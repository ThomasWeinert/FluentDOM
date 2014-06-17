<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class LoaderHtmlTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Html
     */
    public function testSupportsExpectingTrue() {
      $loader = new Html();
      $this->assertTrue($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\Html
     */
    public function testSupportsExpectingFalse() {
      $loader = new Html();
      $this->assertFalse($loader->supports('text/xml'));
    }

    /**
     * @covers FluentDOM\Loader\Html
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
  }
}