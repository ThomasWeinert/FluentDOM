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
require_once(dirname(__FILE__).'/CssTest.php');
require_once(dirname(__FILE__).'/Css/AllTests.php');
require_once(dirname(__FILE__).'/ScannerTest.php');
require_once(dirname(__FILE__).'/TokenTest.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMSelector_AllTests {

  /**
  *
  * @see PHPUnit_Util_Filter::addFileToFilter()
  * @see PHPUnit_Framework_TestSuite::addTestSuite()
  */
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('FluentDOM Selector');
    $suite->addTestSuite('FluentDOMSelectorCss_AllTests');
    $suite->addTestSuite('FluentDOMSelectorCssTest');
    $suite->addTestSuite('FluentDOMSelectorScannerTest');
    $suite->addTestSuite('FluentDOMSelectorTokenTest');
    return $suite;
  }
}
?>