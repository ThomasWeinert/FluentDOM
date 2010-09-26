<?php
/**
* Collection of test for the FluentDOMStyle class supporting PHP 5.2
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* load necessary files
*/
require_once (dirname(__FILE__).'/../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../src/FluentDOM/Style.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMStyle.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMStyleTest extends FluentDOMTestCase {

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
  * @group GlobalFunctions
  */
  public function testFunction() {
    $fd = FluentDOMStyle();
    $this->assertTrue($fd instanceof FluentDOMStyle);
  }

  /**
  * @group GlobalFunctions
  */
  public function testFunctionWithContent() {
    $dom = new DOMDocument();
    $node = $dom->appendChild($dom->createElement('html'));
    $fd = FluentDOMStyle($node);
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $this->assertEquals('html', $fd->document->documentElement->nodeName);
  }


  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::__get
  */
  public function testPropertyCssGet() {
    $fd = $this->getFluentDOMStyleFixture('<sample style="test: success"/>', '/*');
    $css = $fd->css;
    $this->assertType('FluentDOMCss', $css);
    $this->assertAttributeSame(
      $fd, '_fd', $css
    );
    $this->assertAttributeSame(
      array('test' => 'success'), '_properties', $css
    );
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::__set
  */
  public function testPropertyCssSetWithArray() {
    $fd = $this->getFluentDOMStyleFixture('<sample/>', '/*');
    $fd->css = array('foo' => '1', 'bar' => '2');
    $this->assertEquals(
      '<sample style="bar: 2; foo: 1;"/>',
      $fd->document->saveXml($fd->document->documentElement)
    );
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::__set
  */
  public function testPropertyCssSetWithFluentDOMCss() {
    $fd = $this->getFluentDOMStyleFixture('<sample/>', '/*');
    $fd->css = new FluentDOMCss(NULL, 'foo: 1; bar: 2;');
    $this->assertEquals(
      '<sample style="bar: 2; foo: 1;"/>',
      $fd->document->saveXml($fd->document->documentElement)
    );
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssRead() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $this->assertEquals('left', $fd->css('text-align'));
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssReadWithInvalidProperty() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $this->assertEquals(NULL, $fd->css('---'));
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssReadOnEmpty() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML);
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $this->assertEquals(NULL, $fd->css('text-align'));
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssReadOnTextNodes() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div')->contents()->andSelf();
    $this->assertTrue(count($fd) > 3);
    $this->assertEquals('left', $fd->css('text-align'));
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssWriteWithString() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $fd->css('text-align', 'center');
    $this->assertEquals('text-align: center;', $fd->eq(0)->attr('style'));
    $this->assertEquals('text-align: center;', $fd->eq(1)->attr('style'));
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssWriteWithArray() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
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
  * @covers FluentDOMStyle::css
  */
  public function testCssWriteWithCallback() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $fd->css('text-align', array($this, 'callbackTestCssWriteWithCallback'));
    $this->assertEquals('text-align: right;', $fd->eq(0)->attr('style'));
    $this->assertEquals('text-align: left;', $fd->eq(1)->attr('style'));
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssWriteWithInvalidProperty() {
    try {
      $this->getFluentDOMStyleFixture(self::HTML, '//div')->css('---', '');
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssWriteWithInvalidPropertyInArray() {
    try {
      $this->getFluentDOMStyleFixture(self::HTML, '//div')->css(array('---' => ''));
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssRemoveProperty() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
    $fd->css('text-align', '');
    $this->assertFalse($fd[0]->hasAttribute('style'));
  }

  /**
  * @group ManipulationCSS
  * @covers FluentDOMStyle::css
  */
  public function testCssRemoveProperties() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
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
  * @covers FluentDOMStyle::css
  */
  public function testCssSortPropertiesName() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
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
  * @covers FluentDOMStyle::css
  */
  public function testCssSortPropertiesLevels() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
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
  * @covers FluentDOMStyle::css
  */
  public function testCssSortPropertiesPrefix() {
    $fd = $this->getFluentDOMStyleFixture(self::HTML, '//div');
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

  /*
  * Callbacks
  */

  /**
  * @uses testCssWriteWithCallback()
  */
  public function callbackTestCssWriteWithCallback($node, $index, $value) {
    switch ($value) {
    case 'left' :
      return 'right';
    case 'right' :
      return 'left';
    default :
      return 'center';
    }
  }

  /**
  * Fixtures
  */

  /**
  * Get FluentDOMStyle instance with loaded html document using a mock loader
  *
  * @return FluentDOMStyle
  */
  protected function getFluentDOMStyleFixture($string = NULL, $xpath = NULL) {
    $fd = new FluentDOMStyle();
    if (!empty($string)) {
      $dom = new DOMDocument();
      $dom->loadXML($string);
      $loader = $this->getMock('FluentDOMLoader');
      $loader
        ->expects($this->once())
        ->method('load')
        ->with($this->equalTo(''))
        ->will($this->returnValue($dom));
      $fd->setLoaders(array($loader));
      $fd->load('');
      if (!empty($xpath)) {
        $query = new DOMXPath($dom);
        $nodes = $query->evaluate($xpath);
        $fd = $fd->spawn();
        $fd->push($nodes);
      }
    }
    return $fd;
  }
}
?>