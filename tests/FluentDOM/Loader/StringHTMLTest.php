<?php
/**
* HTML string loader test for FluentDOM
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage unitTests
*/

/**
* load necessary files
*/
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../../FluentDOM/Loader/StringHTML.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderStringHTML.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderStringHTMLTest extends PHPUnit_Framework_TestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderStringHTML();
    $fd = $loader->load('<html><body></body></html>', 'text/html');
    $this->assertTrue($fd instanceof DOMDocument);
    $this->assertEquals('html', $fd->documentElement->nodeName);
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderStringHTML();
    $result = $loader->load('html', 'text/html');
    $this->assertFalse($result);
  }
}

?>