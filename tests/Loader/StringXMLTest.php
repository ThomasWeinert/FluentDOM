<?php
/**
* XML string loader test for FluentDOM
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOMLoader
* @subpackage unitTests
*/

/**
* load necessary files
*/
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../Loader/StringXML.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderStringXML.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderStringXMLTest extends PHPUnit_Framework_TestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderStringXML();
    $dom = $loader->load('<sample/>', 'xml');
    $this->assertTrue($dom instanceof DOMDocument);
    $this->assertEquals('sample', $dom->documentElement->nodeName);
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderStringXML();
    $result = $loader->load('foobar', 'xml');
    $this->assertFalse($result);
  }
}

?>