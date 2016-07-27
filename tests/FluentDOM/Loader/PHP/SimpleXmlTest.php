<?php
namespace FluentDOM\Loader\PHP {

  use FluentDOM\Exceptions\InvalidArgument;
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

    public function testLoadFragmentWithString() {
      $loader = new SimpleXml();
      $fragment = $loader->loadFragment('<test/>', 'php/simplexml');
      $this->assertXmlStringEqualsXmlString(
        '<test/>',
        $fragment->ownerDocument->saveXML($fragment)
      );
    }

    public function testLoadFragmentWithSimpleXMLElement() {
      $loader = new SimpleXml();
      $fragment = $loader->loadFragment(new \SimpleXMLElement('<test/>'), 'php/simplexml');
      $this->assertXmlStringEqualsXmlString(
        '<test/>',
        $fragment->ownerDocument->saveXML($fragment)
      );
    }

    /**
     * @covers FluentDOM\Loader\PHP\SimpleXml
     */
    public function testLoadFragmentWithTypeExpectingNull() {
      $loader = new SimpleXml();
      $this->assertNull(
        $loader->loadFragment('', 'unsupported')
      );
    }

    /**
     * @covers FluentDOM\Loader\PHP\SimpleXml
     */
    public function testLoadFragmentWithInvalidSourceExpectingException() {
      $loader = new SimpleXml();
      $this->setExpectedException(
        InvalidArgument::class,
        'Invalid $source argument. Expected: SimpleXMLElement, string'
      );
      $loader->loadFragment(42, 'php/simplexml');
    }
  }
}