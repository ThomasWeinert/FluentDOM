<?php
/**
* DOMDocument loader test for FluentDOMLoaderDOMNode
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
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Loader/DOMNode.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderDOMNode.
*
* @package FluentDOM
* @subpackage UnitTests
*/
class FluentDOMLoaderDOMNodeTest extends FluentDOMTestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderDOMNode();
    $dom = new DOMDocument();
    $node = $dom->appendChild($dom->createElement('root'));
    $contentType = 'text/xml';
    $result = $loader->load($node, $contentType);
    $this->assertTrue($result instanceof DOMNode);
    $this->assertSame('root', $result->tagName);
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderDOMNode();
    $contentType = 'text/xml';
    $result = $loader->load(NULL, $contentType);
    $this->assertNull($result);
  }

  public function testLoadInvalidWithDOMDocument() {
    $dom = new DOMDocument();
    $loader = new FluentDOMLoaderDOMNode();
    $contentType = 'text/xml';
    $result = $loader->load($dom, $contentType);
    $this->assertNull($result);
  }
}

?>