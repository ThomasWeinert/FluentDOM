<?php
/**
* Collection of test for the FluentDOMSelectorToken class supporting PHP 5.2
*
* @version $Id: IteratorTest.php 345 2009-10-19 19:51:37Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* load necessary files
*/
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../../FluentDOM/Selector/Token.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMSelectorToken.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMSelectorTokenTest extends PHPUnit_Framework_TestCase {

	/**
  * @covers FluentDOMSelectorToken::__construct
  * @covers FluentDOMSelectorToken::__get
	*/
  public function testAttributeTypeRead() {
    $token = new FluentDOMSelectorToken(
      23,
      'hello',
      42
    );
    $this->assertEquals(
      23,
      $token->type
    );
  }

  /**
  * @covers FluentDOMSelectorToken::__construct
  * @covers FluentDOMSelectorToken::__get
  */
  public function testAttributeContentRead() {
    $token = new FluentDOMSelectorToken(23, 'hello', 42);
    $this->assertEquals(
      'hello',
      $token->content
    );
  }

  /**
  * @covers FluentDOMSelectorToken::__construct
  * @covers FluentDOMSelectorToken::__get
  */
  public function testAttributeLengthRead() {
    $token = new FluentDOMSelectorToken(23, 'hello', 42);
    $this->assertEquals(
      5,
      $token->length
    );
  }


  /**
  * @covers FluentDOMSelectorToken::__construct
  * @covers FluentDOMSelectorToken::__get
  */
  public function testAttributePositionRead() {
    $token = new FluentDOMSelectorToken(23, 'hello', 42);
    $this->assertEquals(
      42,
      $token->position
    );
  }

  /**
  * @covers FluentDOMSelectorToken::__construct
  * @covers FluentDOMSelectorToken::__get
  */
  public function testAttributeInvalidReadExpectingException() {
    $token = new FluentDOMSelectorToken(23, 'hello', 42);
    try {
      $dummy = $token->invalidAttribute;
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @covers FluentDOMSelectorToken::__set
  */
  public function testAttributeWriteExpectingException() {
    $token = new FluentDOMSelectorToken(23, 'hello', 42);
    try {
      $token->anyAttribute = 'fail';
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  * @covers FluentDOMSelectorToken::__toString
  * @covers FluentDOMSelectorToken::quoteContent
  */
  public function testToString() {
    $token = new FluentDOMSelectorToken(23, 'hello', 42);
    $this->assertEquals(
      "TOKEN#23:42 'hello'",
      (string)$token
    );
  }
}
?>