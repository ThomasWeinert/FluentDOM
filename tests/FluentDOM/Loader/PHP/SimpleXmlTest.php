<?php
namespace FluentDOM\Loader\PHP {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class SimpleXmlTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\PHP\SimpleXml
     */
    public function testSupportsExpectingTrue() {
      $loader = new SimpleXml();
      $this->assertTrue($loader->supports('php/simplexml'));
    }

    /**
     * @covers FluentDOM\Loader\PHP\SimpleXml
     */
    public function testSupportsExpectingFalse() {
      $loader = new SimpleXml();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\PHP\SimpleXml
     */
    public function testLoadWithValidXml() {
      $loader = new SimpleXml();
      $this->assertInstanceOf(
        'DOMDocument',
        $loader->load(
          simplexml_load_string('<xml/>'),
          'php/simplexml'
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\PHP\SimpleXml
     */
    public function testLoadSelectingChildNode() {
      $loader = new SimpleXml();
      $this->assertInstanceOf(
        'DOMDocument',
        $dom = $loader->load(
          simplexml_load_string('<xml><child/></xml>')->child,
          'php/simplexml'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<child/>', $dom->saveXML()
      );
    }
  }
}