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

  use FluentDOM\TestCase;
  use FluentDOM\Exceptions\InvalidSource;

  require_once __DIR__.'/../TestCase.php';

  /**
   * @covers \FluentDOM\Loader\HtmlLoader
   * @covers \FluentDOM\Loader\LoaderSupports
   */
  class HtmlLoaderTest extends TestCase {

    public function testSupportsExpectingTrue(): void {
      $loader = new HtmlLoader();
      $this->assertTrue($loader->supports('text/html'));
    }

    public function testSupportsExpectingFalse(): void {
      $loader = new HtmlLoader();
      $this->assertFalse($loader->supports('text/xml'));
    }

    public function testLoadWithValidHtml(): void {
      $loader = new HtmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
        $loader->load(
          '<html/>',
          'text/html'
        )
      );
    }

    public function testLoadWithoutOptionsAddsElements(): void {
      $loader = new HtmlLoader();
      $result = $loader->load(
        '<div/>',
        'text/html'
      );
      $document = $result->getDocument();
      $this->assertEquals(
        '<html><body><div/></body></html>',
        $document->saveXML($document->documentElement)
      );
    }

    public function testLoadWithOptions(): void {
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
      $loader = new HtmlLoader();
      $result = $loader->load(
        '<div/>',
        'text/html',
        [
          LoaderOptions::LIBXML_OPTIONS => LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
        ]
      );
      $document = $result->getDocument();
      $this->assertEquals(
        '<div/>',
        $document->saveXML($document->documentElement)
      );
    }

    public function testLoadWithValidHtmlFragment(): void {
      $loader = new HtmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
        $result = $loader->load(
          '<div>Test</div>Text<input>',
          'text/html-fragment'
        )
      );
      $this->assertEquals(
        "<div>Test</div>Text<input>",
        $result->getDocument()->saveHtml()
      );
    }

    public function testLoadWithValidHtmlFragmentDefinedByOption(): void {
      $loader = new HtmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
        $result = $loader->load(
          '<div>Test</div>Text<input>',
          'text/html',
          [
            HtmlLoader::IS_FRAGMENT => TRUE
          ]
        )
      );
      $this->assertEquals(
        "<div>Test</div>Text<input>",
        $result->getDocument()->saveHtml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\HtmlLoader
     */
    public function testLoadWithValidHtmlFileAllowFile(): void {
      $loader = new HtmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
        $result = $loader->load(
          __DIR__.'/TestData/loader.html',
          'text/html',
          [LoaderOptions::ALLOW_FILE => TRUE]
        )
      );
      $this->assertEquals(
        'html', $result->getDocument()->documentElement->localName
      );
    }

    /**
     * @covers \FluentDOM\Loader\HtmlLoader
     */
    public function testLoadWithValidHtmlFileForceFile(): void {
      $loader = new HtmlLoader();
      $this->assertInstanceOf(
        LoaderResult::class,
        $result = $loader->load(
          __DIR__.'/TestData/loader.html',
          'text/html',
          [LoaderOptions::IS_FILE => TRUE]
        )
      );
      $this->assertEquals(
        'html', $result->getDocument()->documentElement->localName
      );
    }

    /**
     * @covers \FluentDOM\Loader\HtmlLoader
     */
    public function testLoadWithFileExpectingException(): void {
      $loader = new HtmlLoader();
      $this->expectException(InvalidSource\TypeFile::class);
      $loader->load(
        __DIR__.'/TestData/loader.html',
        'text/html'
      );
    }

    /**
     * @covers \FluentDOM\Loader\HtmlLoader
     */
    public function testLoadWithUnsupportedType(): void {
      $loader = new HtmlLoader();
      $this->assertNull(
        $loader->load(
          __DIR__.'/TestData/loader.xml',
          'text/xml'
        )
      );
    }

    public function testLoadFragmentWithValidHtmlFragment(): void {
      $loader = new HtmlLoader();
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

    /**
     * @covers \FluentDOM\Loader\HtmlLoader
     */
    public function testLoadFragmentWithUnsupportedType(): void {
      $loader = new HtmlLoader();
      $this->assertNull(
        $loader->loadFragment(
          '', 'text/xml'
        )
      );
    }

    public function testLoadWithMultiByteHtml(): void {
      $loader = new HtmlLoader();
      $result = $loader->load(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'.
        '<html><body>你好，世界</body></html>',
        'text/html'
      );
      $this->assertEquals(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
        '<html><body>你好，世界</body></html>'."\n",
        $result->getDocument()->saveHTML()
      );
    }

    public function testLoadWithMultiByteHtmlFragment(): void {
      $loader = new HtmlLoader();
      $result = $loader->load(
        '<div>你好，世界</div>',
        'text/html-fragment'
      );
      $this->assertEquals(
        '<div>你好，世界</div>'."\n",
        $result->getDocument()->saveHTML()
      );
    }

    public function testLoadFragmentWithMultiByteHtml(): void {
      $loader = new HtmlLoader();
      $result = $loader->loadFragment(
        '<div>你好，世界</div>',
        'text/html-fragment'
      );
      $this->assertEquals(
        '<div>你好，世界</div>',
        $result->ownerDocument->saveHtml($result)
      );
    }

    public function testLoadWithMultiByteHtmlDefinedByMetaTag(): void {
      $loader = new HtmlLoader();
      $result = $loader->load(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
        '<html>'."\n".
        '<head><meta charset="utf-8"></head>'."\n".
        '<body>你好，世界</body>'."\n".
        '</html>'."\n",
        'text/html',
        [
          LoaderOptions::ENCODING => 'ascii'
        ]
      );
      $this->assertThat(
        $result->getDocument()->saveHTML(),
        $this->logicalOr(
          $this->equalTo(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
            '<html>'."\n".
            '<head><meta charset="utf-8"></head>'."\n".
            '<body>你好，世界</body>'."\n".
            '</html>'."\n"
          ),
          $this->equalTo(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
            '<html>'.
            '<head><meta charset="utf-8"></head>'.
            '<body>你好，世界</body>'.
            '</html>'."\n"
          )
        )
      );
    }

    public function testLoadWithMultiByteHtmlDefinedByDeprecatedMetaTag(): void {
      $loader = new HtmlLoader();
      $result = $loader->load(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
        '<html>'."\n".
        '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>'."\n".
        '<body>你好，世界</body>'."\n".
        '</html>'."\n",
        'text/html',
        [
          LoaderOptions::ENCODING => 'ascii'
        ]
      );
      $this->assertThat(
        $result->getDocument()->saveHTML(),
        $this->logicalOr(
          $this->equalTo(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
            '<html>'."\n".
            '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>'."\n".
            '<body>你好，世界</body>'."\n".
            '</html>'."\n"
          ),
          $this->equalTo(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
            '<html>'.
            '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>'.
            '<body>你好，世界</body>'.
            '</html>'."\n"
          )
        )
      );
    }

    public function testLoadWithMultiByteHtmlUseExistingDeclaration(): void {
      $loader = new HtmlLoader();
      $result = $loader->load(
        '<?xml encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'.
        '<html>'.
        '<body>你好，世界</body>'.
        '</html>',
        'text/html'
      );
      $this->assertEquals(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
        '<html>'.
        '<body>你好，世界</body>'.
        '</html>'."\n",
        $result->getDocument()->saveHTML()
      );
    }

    public function testLoadWithMultiByteHtmlReplaceExistingDeclaration(): void {
      $loader = new HtmlLoader();
      $result = $loader->load(
        '<?xml encoding="ASCII"?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
        '<html>'."\n".
        '<head><meta charset="utf-8"></head>'."\n".
        '<body>你好，世界</body>'."\n".
        '</html>'."\n",
        'text/html',
        [
          LoaderOptions::ENCODING => 'UTF-8',
          LoaderOptions::FORCE_ENCODING => true
        ]
      );
      $this->assertThat(
        $result->getDocument()->saveHTML(),
        $this->logicalOr(
          $this->equalTo(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
            '<html>'."\n".
            '<head><meta charset="utf-8"></head>'."\n".
            '<body>你好，世界</body>'."\n".
            '</html>'."\n"
          ),
          $this->equalTo(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
            '<html><head><meta charset="utf-8"></head><body>你好，世界</body></html>'."\n"
          )
        )
      );
    }
  }
}
