<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class CssTest extends TestCase {

    const HTML = '
      <html>
        <body>
          <div style="text-align: left;">First</div>
          <div style="text-align: right;">Second</div>
          <div>Third</div>
        </body>
      </html>
    ';
    /**
     * @covers \FluentDOM\Query\Css::__construct
     */
    public function testConstructorWithOwner(): void {
      $fd = $this->getMockBuilder(Query::class)->getMock();
      $css = new Css($fd);
      $this->assertSame($fd, $css->getOwner());
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetExists
     * @covers \FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetExistsExpectingTrue(): void {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      $this->assertTrue(isset($css['width']));
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetExists
     * @covers \FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetExistsExpectingFalse(): void {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      $this->assertFalse(isset($css['height']));
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetExists
     * @covers \FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetExistsWithoutElementExpectingFalse(): void {
      $fd = new Query();
      $css = new Css($fd);
      $this->assertFalse(isset($css['height']));
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetGet
     * @covers \FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetGet(): void {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      $this->assertEquals('21px', $css['width']);
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetGet
     * @covers \FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetGetWithoutElementExpectingFalse(): void {
      $fd = new Query();
      $css = new Css($fd);
      $this->assertFalse($css['height']);
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetSet
     */
    public function testOffsetSetUpdatesAttributes(): void {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      $css['width'] = '42px';
      $this->assertEquals(
        '<sample style="width: 42px;"/>', $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetSet
     */
    public function testOffsetSetRemovesAttributes(): void {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      $css['width'] = '';
      $this->assertEquals(
        '<sample/>', $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetUnset
     */
    public function testOffsetUnset(): void {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px; height: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      unset($css['width']);
      $this->assertEquals(
        '<sample style="height: 21px;"/>', $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css::offsetUnset
     */
    public function testOffsetUnsetRemovesAttributes(): void {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      unset($css['width']);
      $this->assertEquals(
        '<sample/>', $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css::getIterator
     */
    public function testGetIteratorForFirstElement(): void {
      $fd = new Query();
      $fd->document->loadXML('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      $this->assertEquals(
        ['width' => '21px'],
        iterator_to_array($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css::getIterator
     */
    public function testGetIteratorExpectingEmptyIterator(): void {
      $fd = new Query();
      $css = new Css($fd);
      $this->assertEquals(
        [],
        iterator_to_array($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css::count
     */
    public function testCountExpectingTwo(): void {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px; height: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Css($fd);
      $this->assertEquals(
        2, count($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css::count
     */
    public function testCountExpectingZero(): void {
      $fd = new Query();
      $fd = $fd->find('/*');
      $css = new Css($fd);
      $this->assertEquals(
        0, count($css)
      );
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::__get
     */
    public function testPropertyCssGet(): void {
      $fd = $this->getQueryFixtureFromString('<sample style="test: success"/>', '/*');
      $css = $fd->css;
      $this->assertInstanceOf(Css::class, $css);
      $this->assertSame(
        $fd, $css->getOwner()
      );
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::__set
     */
    public function testPropertyCssSetWithArray(): void {
      $fd = $this->getQueryFixtureFromString('<sample/>', '/*');
      $fd->css = ['foo' => '1', 'bar' => '2'];
      $this->assertEquals(
        '<sample style="bar: 2; foo: 1;"/>',
        $fd->document->saveXML($fd->document->documentElement)
      );
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::__set
     */
    public function testPropertyCssSetWithCssObject(): void {
      $fd = $this->getQueryFixtureFromString('<sample/>', '/*');
      $fd->css = new Css\Properties('foo: 1; bar: 2;');
      $this->assertEquals(
        '<sample style="bar: 2; foo: 1;"/>',
        $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssRead(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $this->assertEquals('left', $fd->css('text-align'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssReadWithInvalidProperty(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $this->assertEquals(NULL, $fd->css('---'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssReadOnEmpty(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML);
      $this->assertEquals(NULL, $fd->css('text-align'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssReadOnTextNodes(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div')->contents()->addBack();
      $this->assertCount(6, $fd);
      $this->assertEquals('left', $fd->css('text-align'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssWriteWithString(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css('text-align', 'center');
      $this->assertEquals('text-align: center;', $fd->eq(0)->attr('style'));
      $this->assertEquals('text-align: center;', $fd->eq(1)->attr('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssWriteWithNullValue(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $this->assertSame(
        $fd,
        $fd->css('text-align', NULL)
      );
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithArray(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        [
          'text-align' => 'center',
          'color' => 'black'
        ]
      );
      $this->assertEquals('color: black; text-align: center;', $fd->eq(0)->attr('style'));
      $this->assertEquals('color: black; text-align: center;', $fd->eq(1)->attr('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithCallback(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        'text-align',
        function($node, $index, $value) {
          switch ($value) {
          case 'left' :
            return 'right';
          case 'right' :
            return 'left';
          default :
            return 'center';
          }
        }
      );
      $this->assertEquals('text-align: right;', $fd->eq(0)->attr('style'));
      $this->assertEquals('text-align: left;', $fd->eq(1)->attr('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithInvalidPropertySyntax(): void {
      $this->expectException(\InvalidArgumentException::class);
      $this->getQueryFixtureFromString(self::HTML, '//div')->css('---', '');
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithInvalidPropertyType(): void {
      $this->expectException(\InvalidArgumentException::class);
      $this->getQueryFixtureFromString(self::HTML, '//div')->css(23, '');
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithInvalidPropertyInArray(): void {
      $this->expectException(\InvalidArgumentException::class);
      $this->getQueryFixtureFromString(self::HTML, '//div')->css(['---' => '']);
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssRemoveProperty(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css('text-align', '');
      $this->assertFalse($fd[0]->hasAttribute('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssRemoveProperties(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        [
          'text-align' => '',
          'font-weight' => ''
        ]
      );
      $this->assertFalse($fd[0]->hasAttribute('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssSortPropertiesName(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        [
          'padding' => '0em',
          'margin' => '1em'
        ]
      );
      $expect = 'margin: 1em; padding: 0em;';
      $this->assertEquals($expect, $fd[2]->getAttribute('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssSortPropertiesLevels(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        [
          'border' => '1px solid red',
          'border-top-color' => 'black',
          'border-top' => '2px solid blue'
        ]
      );
      $expect = 'border: 1px solid red; border-top: 2px solid blue; border-top-color: black;';
      $this->assertEquals($expect, $fd[2]->getAttribute('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers \FluentDOM\Query::css
     */
    public function testCssSortPropertiesPrefix(): void {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        [
          '-moz-opacity' => 30,
          '-o-opacity' => 30,
          'opacity' => 30
        ]
      );
      $expect = 'opacity: 30; -moz-opacity: 30; -o-opacity: 30;';
      $this->assertEquals($expect, $fd[2]->getAttribute('style'));
    }
  }
}
