<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\TestCase;
  use FluentDOM\Exceptions\InvalidSource;

  require_once __DIR__.'/../TestCase.php';

  class XmlLoaderTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     */
    public function testSupportsExpectingTrue(): void {
      $loader = new XmlLoader();
      $this->assertTrue($loader->supports('text/xml'));
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     */
    public function testSupportsExpectingFalse(): void {
      $loader = new XmlLoader();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testLoadWithValidXml(): void {
      $loader = new XmlLoader();
      $document = $loader->load(
        '<xml><![CDATA[Test]]></xml>',
        'text/xml'
      )->getDocument();
      $this->assertEquals(
        '<xml><![CDATA[Test]]></xml>',
        $document->documentElement->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testLoadReplacingCdataInXml(): void {
      $loader = new XmlLoader();
      $document = $loader->load(
        '<xml><![CDATA[Test]]></xml>',
        'text/xml',
        [
          XmlLoader::LIBXML_OPTIONS => LIBXML_NOCDATA
        ]
      )->getDocument();
      $this->assertEquals(
        '<xml>Test</xml>',
        $document->documentElement->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testLoadWithValidXmlFileAllowFile(): void {
      $loader = new XmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
        $loader->load(
          __DIR__.'/TestData/loader.xml',
          'text/xml',
          [
            LoaderOptions::ALLOW_FILE => TRUE
          ]
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testLoadWithValidXmlFileForceFile(): void {
      $loader = new XmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
        $loader->load(
          __DIR__.'/TestData/loader.xml',
          'text/xml',
          [
            LoaderOptions::IS_FILE => TRUE
          ]
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\HtmlLoader
     */
    public function testLoadWithFileExpectingException(): void {
      $loader = new XmlLoader();
      $this->expectException(InvalidSource\TypeFile::class);
      $loader->load(
        __DIR__.'/TestData/loader.xml',
        'text/xml'
      );
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     */
    public function testLoadWithUnsupportedType(): void {
      $loader = new XmlLoader();
      $this->assertNull(
        $loader->load(
          __DIR__.'/TestData/loader.html',
          'text/html'
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testLoadWithNonExistingFileExpectingException(): void {
      $loader = new XmlLoader();
      $this->expectException(LoadingError\FileNotLoaded::class);
      $loader->load(
        __DIR__.'/TestData/non-existing.xml',
        'text/xml',
        [
          LoaderOptions::IS_FILE => TRUE
        ]
      );
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testLoadFragmentWithValidXml(): void {
      $loader = new XmlLoader();
      $fragment = $loader->loadFragment(
        'TEXT<xml><![CDATA[Test]]></xml>',
        'text/xml'
      );
      $this->assertEquals(
        'TEXT<xml><![CDATA[Test]]></xml>',
        $fragment->ownerDocument->saveXml($fragment)
      );
    }

    /**
     * @covers \FluentDOM\Loader\XmlLoader
     */
    public function testLoadFragmentWithUnsupportedType(): void {
      $loader = new XmlLoader();
      $this->assertNull(
        $loader->loadFragment(
          '',
          'text/html'
        )
      );
    }

    public function testLoadWithInvalidXmlExpectingException(): void {
      $loader = new XmlLoader();
      $this->expectException(LoadingError\Libxml::class);
      $loader->load('<foo><bar/>', 'text/xml');
    }

    public function testLoadWithPreserveWhitespaceTrue(): void {
      $loader = new XmlLoader();
      $document = $loader
        ->load('<foo> <bar/> </foo>', 'xml', [LoaderOptions::PRESERVE_WHITESPACE => TRUE])
        ->getDocument();
      $this->assertCount(3, $document->documentElement->childNodes);
    }

    public function testLoadWithPreserveWhitespaceFalse(): void {
      $loader = new XmlLoader();
      $document = $loader
        ->load('<foo> <bar/> </foo>', 'xml', [LoaderOptions::PRESERVE_WHITESPACE => FALSE])
        ->getDocument();
      $this->assertCount(1, $document->documentElement->childNodes);
    }
  }
}
