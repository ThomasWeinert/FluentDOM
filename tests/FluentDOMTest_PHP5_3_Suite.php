<?php

/**
 * @package FluentDOM
 * @subpackage unitTests
 */
class FluentDOMTest_PHP5_3_Suite extends PHPUnit_Framework_TestSuite {

    public function __construct() {
        $this->setName('FluentDOM PHP 5.3 Package');
    }

    protected function setUp() {
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
          include_once dirname(__FILE__).'/FluentDOMTest_PHP5_3.php';
          $this->addTestSuite('FluentDOMTest_PHP5_3');
        } else {
          $this->markTestSuiteSkipped("PHP 5.3 required.");
        }
    }

    public static function suite() {
        return new FluentDOMTest_PHP5_3_Suite();
    }
}

?>
