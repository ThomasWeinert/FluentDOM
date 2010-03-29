<?php
/**
* Definition file for the unit test suite for FluentDOM
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage unitTests
*/

/**
* Load necessary files
*/
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/AttributesTest.php');
require_once(dirname(__FILE__).'/DefaultTest.php');
require_once(dirname(__FILE__).'/String/AllTests.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMSelectorCssStatus_AllTests {

  /**
  *
  * @see PHPUnit_Util_Filter::addFileToFilter()
  * @see PHPUnit_Framework_TestSuite::addTestSuite()
  */
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('FluentDOM Selector Css Status');
    $suite->addTestSuite('FluentDOMSelectorCssStatusAttributesTest');
    $suite->addTestSuite('FluentDOMSelectorCssStatusDefaultTest');
    $suite->addTestSuite('FluentDOMSelectorCssStatusString_AllTests');
    return $suite;
  }
}
?>