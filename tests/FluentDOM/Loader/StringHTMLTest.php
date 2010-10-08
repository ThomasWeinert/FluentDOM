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
require_once(dirname(__FILE__).'/../../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Loader/StringHTML.php');

/**
* Test class for FluentDOMLoaderStringHTML.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderStringHTMLTest extends FluentDOMTestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderStringHTML();
    $contentType = 'text/html';
    $fd = $loader->load('<html><body></body></html>', $contentType);
    $this->assertTrue($fd instanceof DOMDocument);
    $this->assertEquals('html', $fd->documentElement->nodeName);
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderStringHTML();
    $contentType = 'text/html';
    $result = $loader->load('html', $contentType);
    $this->assertNull($result);
  }
}

?>
