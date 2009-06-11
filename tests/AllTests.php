<?php
require_once 'PHPUnit/Framework.php';

require_once 'FluentDOMTest.php';
if (version_compare(PHP_VERSION, '5.3', '>=')) {
  include_once 'FluentDOMTest_PHP5_3.php';
}
 
class FluentDOM_AllTests {

  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('FluentDOM Package');

    $suite->addTestSuite('FluentDOMTest');
    if (version_compare(PHP_VERSION, '5.3', '>=')) {
      $suite->addTestSuite('FluentDOMTest_PHP5_3');
    }
 
    return $suite;
  }
}
?>