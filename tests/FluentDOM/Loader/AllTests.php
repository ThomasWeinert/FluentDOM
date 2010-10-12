<?php
/**
* Definition file for the unit test suite for FluentDOM loaders
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/DOMDocumentTest.php');
require_once(dirname(__FILE__).'/DOMNodeTest.php');
require_once(dirname(__FILE__).'/FileHTMLTest.php');
require_once(dirname(__FILE__).'/FileXMLTest.php');
require_once(dirname(__FILE__).'/PDOTest.php');
require_once(dirname(__FILE__).'/SimpleXMLElementTest.php');
require_once(dirname(__FILE__).'/StringHTMLTest.php');
require_once(dirname(__FILE__).'/StringJSONTest.php');
require_once(dirname(__FILE__).'/StringXMLTest.php');

/**
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMLoader_AllTests {

  /**
  *
  * @see PHPUnit_Util_Filter::addFileToFilter()
  * @see PHPUnit_Framework_TestSuite::addTestSuite()
  */
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('FluentDOM Loaders');

    $suite->addTestSuite('FluentDOMLoaderDOMDocumentTest');
    $suite->addTestSuite('FluentDOMLoaderDOMNodeTest');
    $suite->addTestSuite('FluentDOMLoaderFileHTMLTest');
    $suite->addTestSuite('FluentDOMLoaderFileXMLTest');
    $suite->addTestSuite('FluentDOMLoaderPDOTest');
    $suite->addTestSuite('FluentDOMLoaderSimpleXMLElementTest');
    $suite->addTestSuite('FluentDOMLoaderStringHTMLTest');
    $suite->addTestSuite('FluentDOMLoaderStringJSONTest');
    $suite->addTestSuite('FluentDOMLoaderStringXMLTest');

    return $suite;
  }
}
?>
