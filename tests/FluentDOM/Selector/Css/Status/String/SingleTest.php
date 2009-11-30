<?php
/**
* Collection of test for the FluentDOMSelectorCssStatusStringSingle class supporting PHP 5.2
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
require_once dirname(__FILE__).'/../../../../../../FluentDOM/Selector/Css.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMSelectorCssStatusStringSingle.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMSelectorCssStatusStringSingleTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers FluentDOMSelectorCssStatusStringSingle::getToken
  * @dataProvider getTokenDataProvider
  */
  public function testGetToken($string, $expectedToken) {
    $status = new FluentDOMSelectorCssStatusStringSingle();
    $this->assertEquals(
      $status->getToken($string, 0),
      $expectedToken
    );
  }

  /**
  * @covers FluentDOMSelectorCssStatusStringSingle::isEndToken
  */
  public function testIsEndToken() {
    $status = new FluentDOMSelectorCssStatusStringSingle();
    $this->assertTrue(
      $status->isEndToken(
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_SINGLEQUOTE_STRING_END, '"', 0
        )
      )
    );
  }
  /**
  * @covers FluentDOMSelectorCssStatusStringSingle::getNewStatus
  */
  public function testGetNewStatus() {
    $status = new FluentDOMSelectorCssStatusStringSingle();
    $this->assertNULL(
       $status->getNewStatus(NULL)
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
      'single quote string end' => array(
        "'",
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_SINGLEQUOTE_STRING_END, "'", 0
        )
      ),
      'escaped backslash' => array(
        '\\\\',
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_STRING_ESCAPED_CHAR, '\\\\', 0
        )
      ),
      'string chars' => array(
        'abcd',
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_STRING_CHARS, 'abcd', 0
        )
      )
    );
  }
}
?>