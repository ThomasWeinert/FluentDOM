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
require_once 'PHPUnit/Framework.php';
$dir = dirname(__FILE__);
require_once $dir.'/StringXMLTest.php';
require_once $dir.'/FileXMLTest.php';
require_once $dir.'/StringHTMLTest.php';
require_once $dir.'/FileHTMLTest.php';
require_once $dir.'/DOMDocumentTest.php';
require_once $dir.'/DOMNodeTest.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

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

    $suite->addTestSuite('FluentDOMLoaderStringXMLTest');
    $suite->addTestSuite('FluentDOMLoaderStringHTMLTest');
    $suite->addTestSuite('FluentDOMLoaderFileHTMLTest');
    $suite->addTestSuite('FluentDOMLoaderFileXMLTest');
    $suite->addTestSuite('FluentDOMLoaderDOMDocumentTest');
    $suite->addTestSuite('FluentDOMLoaderDOMNodeTest');

    return $suite;
  }
}
?>
