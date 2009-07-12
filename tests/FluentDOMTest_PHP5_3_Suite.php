<?php

/**
 * @package FluentDOM
 * @subpackage unitTests
 */
class FluentDOMTest_PHP5_3_Suite {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('FluentDOM PHP 5.3 Package');

        if (version_compare(PHP_VERSION, '5.3', '>=')) {
          include_once dirname(__FILE__).'/FluentDOMTest_PHP5_3.php';
          $suite->addTestSuite('FluentDOMTest_PHP5_3');
        } else {
          $suite->markTestSuiteSkipped("PHP 5.3 required.");
        }

        return $suite;
    }
}

?>
