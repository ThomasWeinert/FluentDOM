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
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../../FluentDOM/Loader/SimpleXMLElement.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderSimpleXMLElement.
*
* @package FluentDOM
* @subpackage UnitTests
*/
class FluentDOMLoaderSimpleXMLElementTest extends PHPUnit_Framework_TestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderSimpleXMLElement();
    $simpleXML = simplexml_load_string('<root/>');
    $result = $loader->load($simpleXML, 'text/xml');
    $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $result);
    $this->assertTrue($result[0] instanceof DOMDocument);
    $this->assertSame('root', $result[1][0]->tagName);
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderSimpleXMLElement();
    $result = $loader->load(NULL, 'text/xml');
    $this->assertFalse($result);
  }
}

?>