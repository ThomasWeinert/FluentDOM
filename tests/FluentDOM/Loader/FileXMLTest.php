<?php
/**
* XML file loader test for FluentDOM
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
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Loader/FileXML.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderFileXMLTest.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderFileXMLTest extends FluentDOMTestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderFileXML();
    $contentType = 'text/xml';
    $fd = $loader->load(
      dirname(__FILE__).'/data/fileXML.src.xml',
      $contentType
    );
    $this->assertTrue($fd instanceof DOMDocument);
    $this->assertEquals('html', $fd->documentElement->nodeName);
  }

  public function testLoadWithXmlFileInvalid() {
    $loader = new FluentDOMLoaderFileXML();
    $contentType = 'text/xml';
    $result = $loader->load('<invalidFileName />', $contentType);
    $this->assertNull($result);
  }

  public function testLoadInvalid() {
    try {
      $loader = new FluentDOMLoaderFileXML();
      $contentType = 'text/xml';
      $result = $loader->load('invalidFileName', $contentType);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $e) {
    }
  }
}

?>