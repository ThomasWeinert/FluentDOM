<?php
/**
* Collection of test for the FluentDOMSelectorScanner class supporting PHP 5.2
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* load necessary files
*/
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/../../../FluentDOM/Selector/Scanner.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMSelectorScanner.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMSelectorScannerTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers FluentDOMSelectorScanner::__construct
  */
  public function testConstructor() {
    $status = $this->getMock('FluentDOMSelectorStatus');
    $scanner = new FluentDOMSelectorScanner($status);
    $this->assertAttributeSame(
      $status, '_status', $scanner
    );
  }

/**
  * @covers FluentDOMSelectorScanner::scan
  * @covers FluentDOMSelectorScanner::_next
  */
  public function testScanWithSingleValidToken() {
    $token = $this->getTokenMockObjectFixture(6);
    $status = $this->getStatusMockObjectFixture(
      array($token, NULL), // getToken() returns this elements
      FALSE // isEndToken() returns FALSE
    );
    $status
      ->expects($this->once())
      ->method('getNewStatus')
      ->with($this->equalTo($token))
      ->will($this->returnValue(FALSE));

    $scanner = new FluentDOMSelectorScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLE');
    $this->assertEquals(
      array($token),
      $tokens
    );
  }

  /**
  * @covers FluentDOMSelectorScanner::scan
  * @covers FluentDOMSelectorScanner::_next
  */
  public function testScanWithEndToken() {
    $token = $this->getTokenMockObjectFixture(6);
    $status = $this->getStatusMockObjectFixture(
      array($token), // getToken() returns this elements
      TRUE // isEndToken() returns TRUE
    );

    $scanner = new FluentDOMSelectorScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLE');
    $this->assertEquals(
      array($token),
      $tokens
    );
  }

  /**
  * @covers FluentDOMSelectorScanner::scan
  * @covers FluentDOMSelectorScanner::_next
  */
  public function testScanWithInvalidToken() {
  $status = $this->getStatusMockObjectFixture(
      array(NULL) // getToken() returns this elements
    );
    $scanner = new FluentDOMSelectorScanner($status);
    $tokens = array();
    try {
      $scanner->scan($tokens, 'SAMPLE');
      $this->fail('An expected exception has not been occured.');
    } catch (UnexpectedValueException $e) {
    }
  }

  /**
  * @covers FluentDOMSelectorScanner::scan
  * @covers FluentDOMSelectorScanner::_next
  * @covers FluentDOMSelectorScanner::_delegate
  */
  public function testScanWithSubStatus() {
    $tokenOne = $this->getTokenMockObjectFixture(6);
    $tokenTwo = $this->getTokenMockObjectFixture(4);
    $subStatus = $this->getStatusMockObjectFixture(
      array($tokenTwo), // getToken() returns this elements
      TRUE // isEndToken() returns TRUE
    );
    $status = $this->getStatusMockObjectFixture(
      array($tokenOne, NULL), // getToken() returns this elements
      FALSE // isEndToken() returns FALSE
    );
    $status
      ->expects($this->once())
      ->method('getNewStatus')
      ->with($this->equalTo($tokenOne))
      ->will($this->returnValue($subStatus));

    $scanner = new FluentDOMSelectorScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLETEST');
    $this->assertEquals(
      array($tokenOne, $tokenTwo),
      $tokens
    );
  }

  /**
  * @covers FluentDOMSelectorScanner::matchPattern
   */
  public function testMatchPatternExpectingString() {
    $this->assertEquals(
      'y',
      FluentDOMSelectorScanner::matchPattern('xyz', 1, '(y)')
    );
  }
  /**
  * @covers FluentDOMSelectorScanner::matchPattern
   */
  public function testMatchPatternExpectingNull() {
    $this->assertNull(
      FluentDOMSelectorScanner::matchPattern('xyz', 1, '(=)')
    );
  }

  /******************************
  * Fixtures
  ******************************/

  private function getTokenMockObjectFixture($length) {
    $token = $this->getMock('FluentDOMSelectorToken');
    $token
      ->expects($this->any())
      ->method('__get')
      ->will($this->returnValue($length));
    return $token;
  }

  private function getStatusMockObjectFixture($tokens, $isEndToken = NULL) {
    $status = $this->getMock('FluentDOMSelectorStatus');
    if (count($tokens) > 0) {
      $status
        ->expects($this->exactly(count($tokens)))
        ->method('getToken')
        ->with(
          $this->isType('string'),
          $this->isType('integer')
         )
        ->will(
          call_user_func_array(
            array($this, 'onConsecutiveCalls'),
            $tokens
          )
        );
    }
    if (!is_null($isEndToken)) {
    $status
      ->expects($this->any())
      ->method('isEndToken')
      ->with($this->isInstanceOf('FluentDOMSelectorToken'))
      ->will($this->returnValue($isEndToken));
    }
    return $status;
  }
}
?>