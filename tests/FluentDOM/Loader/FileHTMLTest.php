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
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/../../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Loader/FileHTML.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderFileHTMLTest.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderFileHTMLTest extends FluentDOMTestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderFileHTML();
    $contentType = 'text/html';
    $fd = $loader->load(
      dirname(__FILE__).'/data/fileHTML_src.html',
      $contentType
    );
    $this->assertTrue($fd instanceof DOMDocument);
    $this->assertEquals('html', $fd->documentElement->nodeName);
  }

  public function testLoadWithHtmlStringInvalid() {
    $loader = new FluentDOMLoaderFileHTML();
    $contentType = 'text/html';
    $result = $loader->load('<invalidFileName></invalidFileName>', $contentType);
    $this->assertFalse($result);
  }

  public function testLoadInvalid() {
    try {
      $loader = new FluentDOMLoaderFileHTML();
      $contentType = 'text/html';
      $result = $loader->load('invalidFileName', $contentType);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $e) {
    }
  }
}
?>