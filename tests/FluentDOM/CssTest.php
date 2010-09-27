<?php
/**
* Collection of tests for the FluentDOMCss class
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage unitTests
*/

/**
* load necessary files
*/
require_once(dirname(__FILE__).'/../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../src/FluentDOM/Css.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class FluentDOMCssTest extends FluentDOMTestCase {

  /**
  * @covers FluentDOMCss::__construct
  */
  public function testConstructorWithOwner() {
    $fd = $this->getMock('FluentDOMCore');
    $css = new FluentDOMCss($fd);
    $this->assertAttributeSame($fd, '_fd', $css);
  }

  /**
  * @covers FluentDOMCss::__construct
  */
  public function testConstructorWithStyleString() {
    $css = new FluentDOMCss(NULL, 'width: auto;');
    $this->assertAttributeEquals(
      array('width' => 'auto'), '_properties', $css
    );
  }

  /**
  * @covers FluentDOMCss::__toString
  */
  public function testMagicMethodToString() {
    $css = new FluentDOMCss(NULL, 'width: auto;');
    $this->assertEquals(
      'width: auto;', (string)$css
    );
  }

  /**
  * @covers FluentDOMCss::offsetGet
  */
  public function testOffsetGet() {
    $css = new FluentDOMCss(NULL, 'width: auto;');
    $this->assertEquals(
      'auto', $css['width']
    );
  }

  /**
  * @covers FluentDOMCss::offsetExists
  */
  public function testOffsetExistsExpectingTrue() {
    $css = new FluentDOMCss(NULL, 'width: auto;');
    $this->assertTrue(isset($css['width']));
  }

  /**
  * @covers FluentDOMCss::offsetExists
  */
  public function testOffsetExistsExpectingFalse() {
    $css = new FluentDOMCss(NULL, 'width: auto;');
    $this->assertFalse(isset($css['height']));
  }

  /**
  * @covers FluentDOMCss::offsetSet
  */
  public function testOffsetSet() {
    $css = new FluentDOMCss();
    $css['width'] = 'auto';
    $this->assertAttributeEquals(
      array('width' => 'auto'), '_properties', $css
    );
  }

  /**
  * @covers FluentDOMCss::offsetSet
  */
  public function testOffsetWithInvalidName() {
    $css = new FluentDOMCss();
    try {
      $css['---'] = 'test';
      $this->fail('An expected exception has now been thrown.');
    } catch (InvalidArgumentException $e) {
    }
  }

  /**
  * @covers FluentDOMCss::offsetSet
  */
  public function testOffsetSetWithEmptyValue() {
    $css = new FluentDOMCss(NULL, 'width: auto; height: auto;');
    $css['width'] = '';
    $this->assertEquals('height: auto;', (string)$css);
  }

  /**
  * @covers FluentDOMCss::offsetSet
  */
  public function testOffsetSetUpdatesAttributes() {
    $fd = new FluentDOM();
    $fd->load('<sample style="width: 21px;"/>');
    $fd = $fd->find('/*');
    $css = new FluentDOMCss($fd);
    $css['width'] = '42px';
    $this->assertEquals(
      '<sample style="width: 42px;"/>', $fd->document->saveXml($fd->document->documentElement)
    );
  }

  /**
  * @covers FluentDOMCss::offsetSet
  */
  public function testOffsetSetRemovesAttributes() {
    $fd = new FluentDOM();
    $fd->load('<sample style="width: 21px;"/>');
    $fd = $fd->find('/*');
    $css = new FluentDOMCss($fd);
    $css['width'] = '';
    $this->assertEquals(
      '<sample/>', $fd->document->saveXml($fd->document->documentElement)
    );
  }

  /**
  * @covers FluentDOMCss::decode
  * @dataProvider provideStyleStrings
  */
  public function testDecode($expected, $styleString) {
    $css = new FluentDOMCss();
    $this->assertEquals(
      $expected, $css->decode($styleString)
    );
  }

  /**
  * @covers FluentDOMCss::encode
  * @covers FluentDOMCss::_compare
  * @covers FluentDOMCss::_decodeName
  * @dataProvider providePropertyArrays
  */
  public function testEncode($expected, $propertyArray) {
    $css = new FluentDOMCss();
    $this->assertEquals(
      $expected, $css->encode($propertyArray)
    );
  }

  /********************
  * data provider
  ********************/

  public static function provideStyleStrings() {
    return array(
      'single property' => array(
        array('width' => 'auto'),
        'width: auto;'
      )
    );
  }

  public static function providePropertyArrays() {
    return array(
      'single property' => array(
        'width: auto;',
        array('width' => 'auto')
      ),
      'two properties' => array(
        'height: auto; width: auto;',
        array('width' => 'auto', 'height' => 'auto')
      ),
      'detailed properties' => array(
        'margin: 0; margin-top: 10px;',
        array('margin-top' => '10px', 'margin' => '0')
      ),
      'browser properties' => array(
        'box-sizing: border-box; -moz-box-sizing: border-box; -o-box-sizing: border-box;',
        array(
          '-o-box-sizing' => 'border-box',
          'box-sizing' => 'border-box',
          '-moz-box-sizing' => 'border-box'
        )
      )
    );
  }
}