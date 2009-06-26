<?php
/**
* Collection of test for the FluentDOMStyle class supporting PHP 5.2
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOMStyle
* @subpackage unitTests
*/
require_once 'PHPUnit/Framework.php';
require_once '../FluentDOMStyle.php';

/**
* Test class for FluentDOMStyle.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMStyleTest extends PHPUnit_Framework_TestCase {

  const HTML = '
    <html>
      <body>
        <div style="text-align: left;">First</div>
        <div style="text-align: right;">Second</div>
        <div>Third</div>
      </body>
    </html>
  ';

  function testConstructor() {
    $doc = new FluentDOMStyle(self::HTML);
    $this->assertTrue($doc instanceof FluentDOMStyle);
  }

  function testChaining() {
    $doc = FluentDOMStyle(self::HTML);
    $this->assertTrue($doc instanceof FluentDOMStyle);
    $findDoc = $doc->find('//div');
    $this->assertTrue($findDoc instanceof FluentDOMStyle);
    $this->assertTrue($doc !== $findDoc);
  }

  function testCSSRead() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $this->assertEquals('left', $items->css('text-align'));
  }

  function testCSSReadWithInvalidProperty() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $this->assertEquals(NULL, $items->css('---'));
  }

  function testCSSReadOnEmpty() {
    $items = FluentDOMStyle(self::HTML);
    $this->assertTrue($items instanceof FluentDOMStyle);
    $this->assertEquals(NULL, $items->css('text-align'));
  }

  function testCSSReadOnTextNodes() {
    $items = FluentDOMStyle(self::HTML)->find('//div')->children()->andSelf();
    $this->assertTrue(count($items) > 3);
    $this->assertEquals('left', $items->css('text-align'));
  }

  function testCSSWriteWithString() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $items->css('text-align', 'center');
    $this->assertEquals('text-align: center;', $items->eq(0)->attr('style'));
    $this->assertEquals('text-align: center;', $items->eq(1)->attr('style'));
  }

  function testCSSWriteWithArray() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $items->css(
      array(
        'text-align' => 'center',
        'color' => 'black'
      )
    );
    $this->assertEquals('color: black; text-align: center;', $items->eq(0)->attr('style'));
    $this->assertEquals('color: black; text-align: center;', $items->eq(1)->attr('style'));
  }

  function testCSSWriteWithFunction() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $items->css('text-align', array($this, 'callbackTestCSSWriteWithFunction'));
    $this->assertEquals('text-align: right;', $items->eq(0)->attr('style'));
    $this->assertEquals('text-align: left;', $items->eq(1)->attr('style'));
  }

  function testCSSWriteWithInvalidProperty() {
    try {
      FluentDOMStyle(self::HTML)->find('//div')->css('---', '');
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  function testCSSWriteWithInvalidPropertyInArray() {
    try {
      FluentDOMStyle(self::HTML)->find('//div')->css(array('---' => ''));
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  function testCSSRemoveProperty() {
    $doc = FluentDOMStyle(self::HTML)->find('//div');
    $doc->css('text-align', '');
    $this->assertFalse($doc[0]->hasAttribute('style'));
  }

  function testCSSRemoveProperties() {
    $doc = FluentDOMStyle(self::HTML)->find('//div');
    $doc->css(
      array(
        'text-align' => '',
        'font-weight' => ''
      )
    );
    $this->assertFalse($doc[0]->hasAttribute('style'));
  }

  function testCSSSortPropertiesName() {
    $doc = FluentDOMStyle(self::HTML)->find('//div');
    $doc->css(
      array(
       'padding' => '0em',
       'margin' => '1em'
      )
    );
    $expect = 'margin: 1em; padding: 0em;';
    $this->assertEquals($expect, $doc[2]->getAttribute('style'));
  }

  function testCSSSortPropertiesLevels() {
    $doc = FluentDOMStyle(self::HTML)->find('//div');
    $doc->css(
      array(
       'border' => '1px solid red',
       'border-top-color' => 'black',
       'border-top' => '2px solid blue'
      )
    );
    $expect = 'border: 1px solid red; border-top: 2px solid blue; border-top-color: black;';
    $this->assertEquals($expect, $doc[2]->getAttribute('style'));
  }

  function testCSSSortPropertiesPrefix() {
    $doc = FluentDOMStyle(self::HTML)->find('//div');
    $doc->css(
      array(
       '-moz-opacity' => 30,
       '-o-opacity' => 30,
       'opacity' => 30
      )
    );
    $expect = 'opacity: 30; -moz-opacity: 30; -o-opacity: 30;';
    $this->assertEquals($expect, $doc[2]->getAttribute('style'));
  }

  /*
  * helper
  */
  function callbackTestCSSWriteWithFunction($node, $property, $value) {
    switch ($value) {
    case 'left' :
      return 'right';
    case 'right' :
      return 'left';
    default :
      return 'center';
    }
  }
}
?>