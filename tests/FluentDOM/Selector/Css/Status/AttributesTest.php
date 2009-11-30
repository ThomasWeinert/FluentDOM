<?php
/**
* Collection of test for the FluentDOMSelectorCssStatusAttributes class supporting PHP 5.2
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
require_once dirname(__FILE__).'/../../../../../FluentDOM/Selector/Css.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMSelectorCssStatusAttributes.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMSelectorCssStatusAttributesTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers FluentDOMSelectorCssStatusAttributes::getToken
  * @dataProvider getTokenDataProvider
  */
  public function testGetToken($string, $expectedToken) {
    $status = new FluentDOMSelectorCssStatusAttributes();
    $this->assertEquals(
      $status->getToken($string, 0),
      $expectedToken
    );
  }

  /**
  * @covers FluentDOMSelectorCssStatusAttributes::isEndToken
  */
  public function testIsEndToken() {
    $status = new FluentDOMSelectorCssStatusAttributes();
    $this->assertTrue(
      $status->isEndToken(
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_ATTRIBUTES_SELECTOR_END, "]", 0
        )
      )
    );
  }
  /**
  * @covers FluentDOMSelectorCssStatusAttributes::getNewStatus
  * @dataProvider getNewStatusDataProvider
  */
  public function testGetNewStatus($token, $expectedStatus) {
    $status = new FluentDOMSelectorCssStatusAttributes();
    $this->assertEquals(
      $status->getNewStatus($token),
      $expectedStatus
    );
  }


  /*****************************
  * Data provider
  *****************************/

  public static function getTokenDataProvider() {
    return array(
      'empty' => array(
        '',
        NULL
      ),
      'attributes end' => array(
        "]",
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_ATTRIBUTES_SELECTOR_END, "]", 0
        )
      ),
      'simple attribute name' => array(
        "class=",
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_ATTRIBUTE_NAME, "class", 0
        )
      )
    );
  }

  public static function getNewStatusDataProvider() {
    return array(
      'whitespaces - no new status' => array(
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_WHITESPACE, " ", 0
        ),
        NULL
      ),
      'single quote string start' => array(
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_SINGLEQUOTE_STRING_START, "'", 0
        ),
        new FluentDOMSelectorCssStatusStringSingle()
      ),
      'double quote string start' => array(
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_DOUBLEQUOTE_STRING_START, "'", 0
        ),
        new FluentDOMSelectorCssStatusStringDouble()
      )
    );
  }
}
?>