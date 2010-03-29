<?php
/**
* Collection of test for the FluentDOMSelectorCssStatusDefault class supporting PHP 5.2
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
require_once(dirname(__FILE__).'/../../../../../FluentDOM/Selector/Css.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMSelectorCssStatusDefault.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMSelectorCssStatusDefaultTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers FluentDOMSelectorCssStatusDefault::getToken
  * @dataProvider getTokenDataProvider
  */
  public function testGetToken($string, $expectedToken) {
    $status = new FluentDOMSelectorCssStatusDefault();
    $this->assertEquals(
      $status->getToken($string, 0),
      $expectedToken
    );
  }

  /**
  * @covers FluentDOMSelectorCssStatusDefault::isEndToken
  */
  public function testIsEndToken() {
    $status = new FluentDOMSelectorCssStatusDefault();
    $this->assertFalse(
      $status->isEndToken(
        new FluentDOMSelectorCssToken(23, 'foo', 42)
      )
    );
  }

  /**
  * @covers FluentDOMSelectorCssStatusDefault::getNewStatus
  * @dataProvider getNewStatusDataProvider
  */
  public function testGetNewStatus($token, $expectedStatus) {
    $status = new FluentDOMSelectorCssStatusDefault();
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
      'type selector' => array(
        'tag',
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_TYPE_SELECTOR, "tag", 0
        )
      ),
      'id selector' => array(
        '#id',
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_ID_SELECTOR, "#id", 0
        )
      ),
      'class selector' => array(
        '.class',
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_CLASS_SELECTOR, ".class", 0
        )
      ),
      'single quote string start' => array(
        "'test'",
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_SINGLEQUOTE_STRING_START, "'", 0
        )
      ),
      'double quote string start' => array(
        '"test"',
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_DOUBLEQUOTE_STRING_START, '"', 0
        )
      ),
      'attributes start' => array(
        "[attr]",
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_ATTRIBUTES_SELECTOR_START, "[", 0
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
      ),
      'attributes selector start' => array(
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_ATTRIBUTES_SELECTOR_START, "[", 0
        ),
        new FluentDOMSelectorCssStatusAttributes()
      )
    );
  }
}
?>