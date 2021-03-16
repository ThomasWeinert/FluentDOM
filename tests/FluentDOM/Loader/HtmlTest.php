<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\TestCase;
  use FluentDOM\Exceptions\InvalidSource;

  require_once __DIR__.'/../TestCase.php';

  /**
   * @covers \FluentDOM\Loader\Html
   * @covers \FluentDOM\Loader\Supports
   */
  class HtmlTest extends TestCase {

    public function testSupportsExpectingTrue(): void {
      $loader = new Html();
      $this->assertTrue($loader->supports('text/html'));
    }

    public function testSupportsExpectingFalse(): void {
      $loader = new Html();
      $this->assertFalse($loader->supports('text/xml'));
    }

    public function testLoadWithValidHtml(): void {
      $loader = new Html();
      $this->assertInstanceOf(
        Result::class,
        $loader->load(
          '<html/>',
          'text/html'
        )
      );
    }

    public function testLoadWithoutOptionsAddsElements(): void {
      $loader = new Html();
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
      $loader = new Html();
      $result = $loader->load(
        '<div/>',
        'text/html',
        [
          Options::LIBXML_OPTIONS => LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
        ]
      );
      $document = $result->getDocument();
      $this->assertEquals(
        '<div/>',
        $document->saveXML($document->documentElement)
      );
    }

    public function testLoadWithValidHtmlFragment(): void {
      $loader = new Html();
      $this->assertInstanceOf(
        Result::class,
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
      $loader = new Html();
      $this->assertInstanceOf(
        Result::class,
        $result = $loader->load(
          '<div>Test</div>Text<input>',
          'text/html',
          [
            Html::IS_FRAGMENT => TRUE
          ]
        )
      );
      $this->assertEquals(
        "<div>Test</div>Text<input>",
        $result->getDocument()->saveHtml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Html
     */
    public function testLoadWithValidHtmlFileAllowFile(): void {
      $loader = new Html();
      $this->assertInstanceOf(
        Result::class,
        $result = $loader->load(
          __DIR__.'/TestData/loader.html',
          'text/html',
          [Options::ALLOW_FILE => TRUE]
        )
      );
      $this->assertEquals(
        'html', $result->getDocument()->documentElement->localName
      );
    }

    /**
     * @covers \FluentDOM\Loader\Html
     */
    public function testLoadWithValidHtmlFileForceFile(): void {
      $loader = new Html();
      $this->assertInstanceOf(
        Result::class,
        $result = $loader->load(
          __DIR__.'/TestData/loader.html',
          'text/html',
          [Options::IS_FILE => TRUE]
        )
      );
      $this->assertEquals(
        'html', $result->getDocument()->documentElement->localName
      );
    }

    /**
     * @covers \FluentDOM\Loader\Html
     */
    public function testLoadWithFileExpectingException(): void {
      $loader = new Html();
      $this->expectException(InvalidSource\TypeFile::class);
      $loader->load(
        __DIR__.'/TestData/loader.html',
        'text/html'
      );
    }

    /**
     * @covers \FluentDOM\Loader\Html
     */
    public function testLoadWithUnsupportedType(): void {
      $loader = new Html();
      $this->assertNull(
        $loader->load(
          __DIR__.'/TestData/loader.xml',
          'text/xml'
        )
      );
    }

    public function testLoadFragmentWithValidHtmlFragment(): void {
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

    /**
     * @covers \FluentDOM\Loader\Html
     */
    public function testLoadFragmentWithUnsupportedType(): void {
      $loader = new Html();
      $this->assertNull(
        $loader->loadFragment(
          '', 'text/xml'
        )
      );
    }

    public function testLoadWithMultiByteHtml(): void {
      $loader = new Html();
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
      $loader = new Html();
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
      $loader = new Html();
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
      $loader = new Html();
      $result = $loader->load(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
        '<html>'."\n".
        '<head><meta charset="utf-8"></head>'."\n".
        '<body>你好，世界</body>'."\n".
        '</html>'."\n",
        'text/html',
        [
          Options::ENCODING => 'ascii'
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
      $loader = new Html();
      $result = $loader->load(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
        '<html>'."\n".
        '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>'."\n".
        '<body>你好，世界</body>'."\n".
        '</html>'."\n",
        'text/html',
        [
          Options::ENCODING => 'ascii'
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
      $loader = new Html();
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
      $loader = new Html();
      $result = $loader->load(
        '<?xml encoding="ASCII"?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n".
        '<html>'."\n".
        '<head><meta charset="utf-8"></head>'."\n".
        '<body>你好，世界</body>'."\n".
        '</html>'."\n",
        'text/html',
        [
          Options::ENCODING => 'UTF-8',
          Options::FORCE_ENCODING => true
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
