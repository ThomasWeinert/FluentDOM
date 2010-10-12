<?php
/**
* Collection of tests for the FluentDOMCssProperties class
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
require_once(dirname(__FILE__).'/../../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Css/Properties.php');

class FluentDOMCssPropertiesTest extends FluentDOMTestCase {

  /**
  * @covers FluentDOMCssProperties::__construct
  */
  public function testConstructor() {
    $css = new FluentDOMCssProperties('width: auto;');
    $this->assertAttributeEquals(
      array('width' => 'auto'), '_properties', $css
    );
  }

  /**
  * @covers FluentDOMCssProperties::__toString
  */
  public function testMagicMethodToString() {
    $css = new FluentDOMCssProperties('width: auto;');
    $this->assertEquals(
      'width: auto;', (string)$css
    );
  }

  /**
  * @covers FluentDOMCssProperties::offsetGet
  */
  public function testOffsetGet() {
    $css = new FluentDOMCssProperties('width: auto;');
    $this->assertEquals(
      'auto', $css['width']
    );
  }

  /**
  * @covers FluentDOMCssProperties::offsetExists
  */
  public function testOffsetExistsExpectingTrue() {
    $css = new FluentDOMCssProperties('width: auto;');
    $this->assertTrue(isset($css['width']));
  }

  /**
  * @covers FluentDOMCssProperties::offsetExists
  */
  public function testOffsetExistsExpectingFalse() {
    $css = new FluentDOMCssProperties('width: auto;');
    $this->assertFalse(isset($css['height']));
  }

  /**
  * @covers FluentDOMCssProperties::offsetSet
  */
  public function testOffsetSet() {
    $css = new FluentDOMCssProperties();
    $css['width'] = 'auto';
    $this->assertAttributeEquals(
      array('width' => 'auto'), '_properties', $css
    );
  }

  /**
  * @covers FluentDOMCssProperties::offsetSet
  */
  public function testOffsetSetWithInvalidName() {
    $css = new FluentDOMCssProperties();
    try {
      $css['---'] = 'test';
      $this->fail('An expected exception has now been thrown.');
    } catch (InvalidArgumentException $e) {
    }
  }

  /**
  * @covers FluentDOMCssProperties::offsetSet
  */
  public function testOffsetSetWithEmptyValue() {
    $css = new FluentDOMCssProperties('width: auto; height: auto;');
    $css['width'] = '';
    $this->assertEquals('height: auto;', (string)$css);
  }

  /**
  * @covers FluentDOMCssProperties::setStyleString
  * @dataProvider provideStyleStrings
  */
  public function testSetStyleString($expected, $styleString) {
    $css = new FluentDOMCssProperties();
    $css->setStyleString($styleString);
    $this->assertAttributeEquals(
      $expected, '_properties', $css
    );
  }

  /**
  * @covers FluentDOMCssProperties::getStyleString
  * @covers FluentDOMCssProperties::_compare
  * @covers FluentDOMCssProperties::_decodeName
  * @dataProvider providePropertyArrays
  */
  public function testGetStyleString($expected, $propertyArray) {
    $css = new FluentDOMCssProperties();
    foreach ($propertyArray as $name => $value) {
      $css[$name] = $value;
    }
    $this->assertEquals(
      $expected, $css->getStyleString($propertyArray)
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