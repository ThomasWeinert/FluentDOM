<?php
namespace FluentDOM\Loader\PHP {

  use FluentDOM\Loader\Result;
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
        Result::class,
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
        Result::class,
        $result = $loader->load(
          simplexml_load_string('<xml><child/></xml>')->child,
          'php/simplexml'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<child/>', $result->getDocument()->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Loader\PHP\SimpleXml
     */
    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new SimpleXml();
      $this->assertNull(
        $loader->load('', 'php/simplexml')
      );
    }
  }
}