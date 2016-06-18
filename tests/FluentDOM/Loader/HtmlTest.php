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
    public function testLoadWithValidHtml() {
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
     * @covers FluentDOM\Loader\Supports
     */
    public function testLoadWithoutOptionsAddsElements() {
      $loader = new Html();
      $document = $loader->load(
        '<div/>',
        'text/html'
      );
      $this->assertEquals(
        '<html><body><div/></body></html>',
        $document->documentElement->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Loader\Html
     * @covers FluentDOM\Loader\Supports
     */
    public function testLoadWithOptions() {
      if (
        !(
          defined('LIBXML_HTML_NODEFDTD') &&
          defined('LIBXML_HTML_NOIMPLIED') &&
          defined('LIBXML_DOTTED_VERSION') &&
          version_compare(LIBXML_DOTTED_VERSION, '2.9', '>=')
        )
      ) {
        $this->markTestSkipped('LibXML options not available, LibXML version: '.LIBXML_DOTTED_VERSION);
      }
      $loader = new Html();
      $document = $loader->load(
        '<div/>',
        'text/html',
        [
          Html::LIBXML_OPTIONS => LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
        ]
      );
      $this->assertEquals(
        '<div/>',
        $document->documentElement->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Loader\Html
     * @covers FluentDOM\Loader\Supports
     */
    public function testLoadWithValidHtmlFragment() {
      $loader = new Html();
      $this->assertInstanceOf(
        'DOMDocument',
        $document = $loader->load(
          '<div>Test</div>Text<input>',
          'text/html-fragment'
        )
      );
      $this->assertEquals(
        "<div>Test</div>Text<input>\n",
        $document->saveHtml()
      );
    }

    /**
     * @covers FluentDOM\Loader\Html
     */
    public function testLoadWithValidHtmlFile() {
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

    /**
     * @covers FluentDOM\Loader\Html
     * @covers FluentDOM\Loader\Supports
     */
    public function testLoadFragmentWithValidHtmlFragment() {
      $loader = new Html();
      $this->assertInstanceOf(
        'DOMDocumentFragment',
        $fragment = $loader->loadFragment(
          '<div>Test</div>Text<input>',
          'text/html-fragment'
        )
      );
      $this->assertEquals(
        "<div>Test</div>Text<input>",
        $fragment->ownerDocument->saveHtml($fragment)
      );
    }
  }
}