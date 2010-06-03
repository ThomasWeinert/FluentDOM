<?php
/**
* DOMDocument loader test for FluentDOMLoaderSimpleXMLElement
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage UnitTests
*/

/**
* load necessary files
*/
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/../../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Loader/SimpleXMLElement.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderSimpleXMLElement.
*
* @package FluentDOM
* @subpackage UnitTests
*/
class FluentDOMLoaderSimpleXMLElementTest extends FluentDOMTestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderSimpleXMLElement();
    $simpleXML = simplexml_load_string('<root/>');
    $contentType = 'text/xml';
    $result = $loader->load($simpleXML, $contentType);
    $this->assertTrue($result instanceof DOMNode);
    $this->assertSame('root', $result->tagName);
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderSimpleXMLElement();
    $contentType = 'text/xml';
    $result = $loader->load(NULL, $contentType);
    $this->assertNull($result);
  }
}

?>