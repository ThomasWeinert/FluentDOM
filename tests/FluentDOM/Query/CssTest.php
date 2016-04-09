<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

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
     * @covers FluentDOM\Query\Css::__construct
     */
    public function testConstructorWithOwner() {
      $fd = $this->getMock('FluentDOM\\Query');
      $css = new Query\Css($fd);
      $this->assertAttributeSame($fd, '_fd', $css);
    }

    /**
     * @covers FluentDOM\Query\Css::offsetExists
     * @covers FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetExistsExpectingTrue() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      $this->assertTrue(isset($css['width']));
    }

    /**
     * @covers FluentDOM\Query\Css::offsetExists
     * @covers FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetExistsExpectingFalse() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      $this->assertFalse(isset($css['height']));
    }

    /**
     * @covers FluentDOM\Query\Css::offsetExists
     * @covers FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetExistsWithoutElementExpectingFalse() {
      $fd = new Query();
      $css = new Query\Css($fd);
      $this->assertFalse(isset($css['height']));
    }

    /**
     * @covers FluentDOM\Query\Css::offsetGet
     * @covers FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetGet() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      $this->assertEquals('21px', $css['width']);
    }

    /**
     * @covers FluentDOM\Query\Css::offsetGet
     * @covers FluentDOM\Query\Css::getStyleProperties
     */
    public function testOffsetGetWithoutElementExpectingFalse() {
      $fd = new Query();
      $css = new Query\Css($fd);
      $this->assertFalse($css['height']);
    }

    /**
     * @covers FluentDOM\Query\Css::offsetSet
     */
    public function testOffsetSetUpdatesAttributes() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      $css['width'] = '42px';
      $this->assertEquals(
        '<sample style="width: 42px;"/>', $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Query\Css::offsetSet
     */
    public function testOffsetSetRemovesAttributes() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      $css['width'] = '';
      $this->assertEquals(
        '<sample/>', $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Query\Css::offsetUnset
     */
    public function testOffsetUnset() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px; height: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      unset($css['width']);
      $this->assertEquals(
        '<sample style="height: 21px;"/>', $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Query\Css::offsetUnset
     */
    public function testOffsetUnsetRemovesAttributes() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      unset($css['width']);
      $this->assertEquals(
        '<sample/>', $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Query\Css::getIterator
     */
    public function testGetIteratorForFirstElement() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      $this->assertEquals(
        array('width' => '21px'),
        iterator_to_array($css)
      );
    }

    /**
     * @covers FluentDOM\Query\Css::getIterator
     */
    public function testGetIteratorExpectingEmptyIterator() {
      $fd = new Query();
      $css = new Query\Css($fd);
      $this->assertEquals(
        array(),
        iterator_to_array($css)
      );
    }

    /**
     * @covers FluentDOM\Query\Css::count
     */
    public function testCountExpectingTwo() {
      $fd = new Query();
      $fd->document->loadXml('<sample style="width: 21px; height: 21px;"/>');
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      $this->assertEquals(
        2, count($css)
      );
    }

    /**
     * @covers FluentDOM\Query\Css::count
     */
    public function testCountExpectingZero() {
      $fd = new Query();
      $fd = $fd->find('/*');
      $css = new Query\Css($fd);
      $this->assertEquals(
        0, count($css)
      );
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::__get
     */
    public function testPropertyCssGet() {
      $fd = $this->getQueryFixtureFromString('<sample style="test: success"/>', '/*');
      $css = $fd->css;
      $this->assertInstanceOf('FluentDOM\\Query\\Css', $css);
      $this->assertAttributeSame(
        $fd, '_fd', $css
      );
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::__set
     */
    public function testPropertyCssSetWithArray() {
      $fd = $this->getQueryFixtureFromString('<sample/>', '/*');
      $fd->css = array('foo' => '1', 'bar' => '2');
      $this->assertEquals(
        '<sample style="bar: 2; foo: 1;"/>',
        $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::__set
     */
    public function testPropertyCssSetWithCssObject() {
      $fd = $this->getQueryFixtureFromString('<sample/>', '/*');
      $fd->css = new Css\Properties('foo: 1; bar: 2;');
      $this->assertEquals(
        '<sample style="bar: 2; foo: 1;"/>',
        $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssRead() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $this->assertEquals('left', $fd->css('text-align'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssReadWithInvalidProperty() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $this->assertEquals(NULL, $fd->css('---'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssReadOnEmpty() {
      $fd = $this->getQueryFixtureFromString(self::HTML);
      $this->assertEquals(NULL, $fd->css('text-align'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssReadOnTextNodes() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div')->contents()->andSelf();
      $this->assertCount(6, $fd);
      $this->assertEquals('left', $fd->css('text-align'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssWriteWithString() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css('text-align', 'center');
      $this->assertEquals('text-align: center;', $fd->eq(0)->attr('style'));
      $this->assertEquals('text-align: center;', $fd->eq(1)->attr('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssWriteWithNullValue() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $this->assertSame(
        $fd,
        $fd->css('text-align', NULL)
      );
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     * @covers FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithArray() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        array(
          'text-align' => 'center',
          'color' => 'black'
        )
      );
      $this->assertEquals('color: black; text-align: center;', $fd->eq(0)->attr('style'));
      $this->assertEquals('color: black; text-align: center;', $fd->eq(1)->attr('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     * @covers FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithCallback() {
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
     * @covers FluentDOM\Query::css
     * @covers FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithInvalidPropertySyntax() {
      $this->setExpectedException('InvalidArgumentException');
      $this->getQueryFixtureFromString(self::HTML, '//div')->css('---', '');
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     * @covers FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithInvalidPropertyType() {
      $this->setExpectedException('InvalidArgumentException');
      $this->getQueryFixtureFromString(self::HTML, '//div')->css(23, '');
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     * @covers FluentDOM\Query::getSetterValues
     */
    public function testCssWriteWithInvalidPropertyInArray() {
      $this->setExpectedException('InvalidArgumentException');
      $this->getQueryFixtureFromString(self::HTML, '//div')->css(array('---' => ''));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssRemoveProperty() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css('text-align', '');
      $this->assertFalse($fd[0]->hasAttribute('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssRemoveProperties() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        array(
          'text-align' => '',
          'font-weight' => ''
        )
      );
      $this->assertFalse($fd[0]->hasAttribute('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssSortPropertiesName() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        array(
          'padding' => '0em',
          'margin' => '1em'
        )
      );
      $expect = 'margin: 1em; padding: 0em;';
      $this->assertEquals($expect, $fd[2]->getAttribute('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssSortPropertiesLevels() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        array(
          'border' => '1px solid red',
          'border-top-color' => 'black',
          'border-top' => '2px solid blue'
        )
      );
      $expect = 'border: 1px solid red; border-top: 2px solid blue; border-top-color: black;';
      $this->assertEquals($expect, $fd[2]->getAttribute('style'));
    }

    /**
     * @group ManipulationCSS
     * @covers FluentDOM\Query::css
     */
    public function testCssSortPropertiesPrefix() {
      $fd = $this->getQueryFixtureFromString(self::HTML, '//div');
      $fd->css(
        array(
          '-moz-opacity' => 30,
          '-o-opacity' => 30,
          'opacity' => 30
        )
      );
      $expect = 'opacity: 30; -moz-opacity: 30; -o-opacity: 30;';
      $this->assertEquals($expect, $fd[2]->getAttribute('style'));
    }
  }
}