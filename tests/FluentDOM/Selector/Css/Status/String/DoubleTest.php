<?php
/**
* Collection of test for the FluentDOMSelectorCssStatusStringDouble class supporting PHP 5.2
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
require_once(dirname(__FILE__).'/../../../../../../FluentDOM/Selector/Css.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMSelectorCssStatusStringDouble.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMSelectorCssStatusStringDoubleTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers FluentDOMSelectorCssStatusStringDouble::getToken
  * @dataProvider getTokenDataProvider
  */
  public function testGetToken($string, $expectedToken) {
    $status = new FluentDOMSelectorCssStatusStringDouble();
    $this->assertEquals(
      $status->getToken($string, 0),
      $expectedToken
    );
  }

  /**
  * @covers FluentDOMSelectorCssStatusStringDouble::isEndToken
  */
  public function testIsEndToken() {
    $status = new FluentDOMSelectorCssStatusStringDouble();
    $this->assertTrue(
      $status->isEndToken(
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_DOUBLEQUOTE_STRING_END, '"', 0
        )
      )
    );
  }
  /**
  * @covers FluentDOMSelectorCssStatusStringDouble::getNewStatus
  */
  public function testGetNewStatus() {
    $status = new FluentDOMSelectorCssStatusStringDouble();
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
      'double quote string end' => array(
        '"',
        new FluentDOMSelectorCssToken(
          FluentDOMSelectorCssToken::TOKEN_DOUBLEQUOTE_STRING_END, '"', 0
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