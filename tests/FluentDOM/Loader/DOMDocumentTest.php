<?php
/**
* DOMDocument loader test for FluentDOM
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
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Loader/DOMDocument.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderDOMDocument.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderDOMDocumentTest extends FluentDOMTestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderDOMDocument();
    $contentType = 'text/xml';
    $fd = $loader->load(new DOMDocument(), $contentType);
    $this->assertTrue($fd instanceof DOMDocument);
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderDOMDocument();
    $contentType = 'text/xml';
    $result = $loader->load(NULL, $contentType);
    $this->assertNull($result);
  }
}

?>