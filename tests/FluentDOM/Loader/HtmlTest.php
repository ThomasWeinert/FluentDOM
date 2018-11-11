<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\TestCase;
  use FluentDOM\Exceptions\InvalidSource;

  require_once __DIR__.'/../TestCase.php';

  class HtmlTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Html
     */
    public function testSupportsExpectingTrue() {
      $loader = new Html();
      $this->assertTrue($loader->supports('text/html'));
    }

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testSupportsExpectingFalse() {
      $loader = new Html();
      $this->assertFalse($loader->supports('text/xml'));
    }

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithValidHtml() {
      $loader = new Html();
      $this->assertInstanceOf(
        Result::class,
        $loader->load(
          '<html/>',
          'text/html'
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithoutOptionsAddsElements() {
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithValidHtmlFragment() {
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithValidHtmlFragmentDefinedByOption() {
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
    public function testLoadWithValidHtmlFileAllowFile() {
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
    public function testLoadWithValidHtmlFileForceFile() {
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
    public function testLoadWithFileExpectingException() {
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
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
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

    /**
     * @covers \FluentDOM\Loader\Html
     */
    public function testLoadFragmentWithUnsupportedType() {
      $loader = new Html();
      $this->assertNull(
        $loader->loadFragment(
          '', 'text/xml'
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithMultiByteHtml() {
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithMultiByteHtmlFragment() {
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadFragmentWithMultiByteHtml() {
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithMultiByteHtmlDefinedByMetaTag() {
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithMultiByteHtmlDefinedByDeprecatedMetaTag() {
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithMultiByteHtmlUseExistingPi() {
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

    /**
     * @covers \FluentDOM\Loader\Html
     * @covers \FluentDOM\Loader\Supports
     */
    public function testLoadWithMultiByteHtmlReplaceExistingPi() {
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
