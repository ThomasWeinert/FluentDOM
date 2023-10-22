<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\PHP {

  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\Loader\LoaderResult;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class SimpleXmlLoaderTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\PHP\SimpleXmlLoader
     */
    public function testSupportsExpectingTrue(): void {
      $loader = new SimpleXmlLoader();
      $this->assertTrue($loader->supports('php/simplexml'));
    }

    /**
     * @covers \FluentDOM\Loader\PHP\SimpleXmlLoader
     */
    public function testSupportsExpectingFalse(): void {
      $loader = new SimpleXmlLoader();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers \FluentDOM\Loader\PHP\SimpleXmlLoader
     */
    public function testLoadWithValidXml(): void {
      $loader = new SimpleXmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
        $loader->load(
          simplexml_load_string('<xml/>'),
          'php/simplexml'
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\PHP\SimpleXmlLoader
     */
    public function testLoadSelectingChildNode(): void {
      $loader = new SimpleXmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
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
     * @covers \FluentDOM\Loader\PHP\SimpleXmlLoader
     */
    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new SimpleXmlLoader();
      $this->assertNull(
        $loader->load('', 'php/simplexml')
      );
    }

    public function testLoadFragmentWithString(): void {
      $loader = new SimpleXmlLoader();
      $fragment = $loader->loadFragment('<test/>', 'php/simplexml');
      $this->assertXmlStringEqualsXmlString(
        '<test/>',
        $fragment->ownerDocument->saveXML($fragment)
      );
    }

    public function testLoadFragmentWithSimpleXMLElement(): void {
      $loader = new SimpleXmlLoader();
      $fragment = $loader->loadFragment(new \SimpleXMLElement('<test/>'), 'php/simplexml');
      $this->assertXmlStringEqualsXmlString(
        '<test/>',
        $fragment->ownerDocument->saveXML($fragment)
      );
    }

    /**
     * @covers \FluentDOM\Loader\PHP\SimpleXmlLoader
     */
    public function testLoadFragmentWithTypeExpectingNull(): void {
      $loader = new SimpleXmlLoader();
      $this->assertNull(
        $loader->loadFragment('', 'unsupported')
      );
    }

    /**
     * @covers \FluentDOM\Loader\PHP\SimpleXmlLoader
     */
    public function testLoadFragmentWithInvalidSourceExpectingException(): void {
      $loader = new SimpleXmlLoader();
      $this->expectException(InvalidArgument::class);
      $this->expectExceptionMessage('Invalid $source argument. Expected: SimpleXMLElement, string');
      $loader->loadFragment(42, 'php/simplexml');
    }
  }
}
