<?php
/**
* Definition file for the unit test suite for FluentDOM css
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/PropertiesTest.php');

/**
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMCss_AllTests {

  /**
  *
  * @see PHPUnit_Util_Filter::addFileToFilter()
  * @see PHPUnit_Framework_TestSuite::addTestSuite()
  */
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('FluentDOM Css');

    $suite->addTestSuite('FluentDOMCssPropertiesTest');

    return $suite;
  }
}
?>
