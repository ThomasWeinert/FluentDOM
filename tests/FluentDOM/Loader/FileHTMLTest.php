<?php
/**
* HTML file loader test for FluentDOM
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
require_once dirname(__FILE__).'/../../../FluentDOM/Loader/FileHTML.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderFileHTMLTest.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderFileHTMLTest extends PHPUnit_Framework_TestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderFileHTML();
    $fd = $loader->load(
      dirname(__FILE__).'/data/fileHTML_src.html',
      'text/html'
    );
    $this->assertTrue($fd instanceof DOMDocument);
    $this->assertEquals('html', $fd->documentElement->nodeName);
  }

  public function testLoadWithHtmlStringInvalid() {
    $loader = new FluentDOMLoaderFileHTML();
    $result = $loader->load('<invalidFileName></invalidFileName>', 'text/html');
    $this->assertFalse($result);
  }

  public function testLoadInvalid() {
    try {
      $loader = new FluentDOMLoaderFileHTML();
      $result = $loader->load('invalidFileName', 'text/html');
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $e) {
    }
  }
}
?>