<?php
/**
* Collection of tests for the FluentDOMCore class
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
require_once(dirname(__FILE__).'/../FluentDOMTestCase.php');

/**
* Test class for FluentDOM.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMCoreTest_PHP5_3 extends PHPUnit_Framework_TestCase {

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isCallback
  */
  public function testIsCallbackWithClosureExpectingTrue() {
    $closure = function() {
    };
    $fd = new FluentDOMCoreProxy_PHP5_3();
    $this->assertTrue(
      $fd->_isCallback($closure, FALSE, FALSE)
    );
  }
}

/******************************
* Proxy
******************************/

class FluentDOMCoreProxy_PHP5_3 extends FluentDOMCore {

  public function _isCallback($callback, $allowGlobalFunctions, $silent) {
    return parent::_isCallback($callback, $allowGlobalFunctions, $silent);
  }
}
