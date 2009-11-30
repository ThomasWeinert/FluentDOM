<?php
/**
* Collection of test for the FluentDOMSelectorCss integration file supporting PHP 5.2
*
* @version $Id: IteratorTest.php 345 2009-10-19 19:51:37Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* load necessary files
*/
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../../FluentDOM/Selector/Css.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMSelectorCss integration file.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMSelectorCssTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers stdClass
  * @dataProvider scannerDataProvider
  */
  public function testScanner($string, $expected) {
    $scanner = new FluentDOMSelectorScanner(new FluentDOMSelectorCssStatusDefault);
    $tokens = array();
    $scanner->scan($tokens, $string);
    $this->assertTokenListEqualsStringList(
      $expected,
      $tokens
    );
  }

  /*****************************
  * Individual assertions
  *****************************/

  public function assertTokenListEqualsStringList($expected, $tokens) {
    $string = array();
    foreach ($tokens as $token) {
      $strings[] = (string)$token;
    }
    $this->assertEquals(
      $expected,
      $strings
    );
  }

  /*****************************
  * Data provider
  *****************************/

  public static function scannerDataProvider() {
    return array(
      array(
        "test",
        array(
          "CSS_TOKEN#1:0 'test'"
        )
      ),
      array(
        "test'string'",
        array(
          "CSS_TOKEN#1:0 'test'",
          "CSS_TOKEN#100:4 '\''",
          "CSS_TOKEN#110:5 'string'",
          "CSS_TOKEN#101:11 '\''"
        )
      ),
      array(
        'div#id.class1.class2:has(span.title)',
        array(
          "CSS_TOKEN#1:0 'div'",
          "CSS_TOKEN#3:3 '#id'",
          "CSS_TOKEN#2:6 '.class1'",
          "CSS_TOKEN#2:13 '.class2'",
          "CSS_TOKEN#4:20 ':has'",
          "CSS_TOKEN#31:24 '('",
          "CSS_TOKEN#1:25 'span'",
          "CSS_TOKEN#2:29 '.title'",
          "CSS_TOKEN#32:35 ')'"
        )
      )
    );
  }
}
?>