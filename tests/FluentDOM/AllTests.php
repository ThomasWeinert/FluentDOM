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
require_once(dirname(__FILE__).'/CssTest.php');
require_once(dirname(__FILE__).'/CoreTest.php');
if (version_compare(PHP_VERSION, '5.3', '>=')) {
  include_once(dirname(__FILE__).'/CoreTest_PHP5_3.php');
}
require_once(dirname(__FILE__).'/HandlerTest.php');
require_once(dirname(__FILE__).'/IteratorTest.php');
require_once(dirname(__FILE__).'/Loader/AllTests.php');
require_once(dirname(__FILE__).'/StyleTest.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMClasses_AllTests {

  /**
  *
  * @see PHPUnit_Util_Filter::addFileToFilter()
  * @see PHPUnit_Framework_TestSuite::addTestSuite()
  */
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('FluentDOM Classes');
    $suite->addTestSuite('FluentDOMAttributesTest');
    $suite->addTestSuite('FluentDOMCssTest');
    $suite->addTestSuite('FluentDOMCoreTest');
    if (version_compare(PHP_VERSION, '5.3', '>=')) {
      $suite->addTestSuite('FluentDOMCoreTest_PHP5_3');
    }
    $suite->addTestSuite('FluentDOMHandlerTest');
    $suite->addTestSuite('FluentDOMIteratorTest');
    $suite->addTestSuite('FluentDOMLoader_AllTests');
    $suite->addTestSuite('FluentDOMStyleTest');
    return $suite;
  }
}
?>