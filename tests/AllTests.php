<?php
/**
* Definition file for the unit test suite for FluentDOM
*
* @version $Id $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
require_once 'PHPUnit/Framework.php';

require_once 'FluentDOMTest.php';
if (version_compare(PHP_VERSION, '5.3', '>=')) {
  include_once 'FluentDOMTest_PHP5_3.php';
}
require_once 'FluentDOMStyleTest.php';

class FluentDOM_AllTests {

  public static function suite() {
    PHPUnit_Util_Filter::addFileToFilter('AllTests.php');
    
    $suite = new PHPUnit_Framework_TestSuite('FluentDOM Package');

    $suite->addTestSuite('FluentDOMTest');
    if (version_compare(PHP_VERSION, '5.3', '>=')) {
      $suite->addTestSuite('FluentDOMTest_PHP5_3');
    }
    $suite->addTestSuite('FluentDOMStyleTest');

    return $suite;
  }
}
?>