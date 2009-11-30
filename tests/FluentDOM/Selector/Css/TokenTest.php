<?php
/**
* Collection of test for the FluentDOMSelectorCssToken class supporting PHP 5.2
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
require_once dirname(__FILE__).'/../../../../FluentDOM/Selector/Css.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMSelectorCssToken.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMSelectorCssTokenTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers FluentDOMSelectorCssToken::__toString
  */
  public function testToString() {
    $token = new FluentDOMSelectorCssToken(
      FluentDOMSelectorCssToken::TOKEN_STRING_CHARS, 'hello', 42);
    $this->assertEquals(
      "CSS_TOKEN#110:42 'hello'",
      $token->__toString()
    );
  }
}
?>