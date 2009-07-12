<?php
/**
* DOMDocument loader test for FluentDOM
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
require_once dirname(__FILE__).'/../../Loader/DOMDocument.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderDOMDocument.
*
* @package FluentDOMLoader
* @subpackage unitTests
*/
class FluentDOMLoaderDOMDocumentTest extends PHPUnit_Framework_TestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderDOMDocument();
    $fd = $loader->load(new DOMDocument(), NULL);
    $this->assertTrue($fd instanceof DOMDocument);
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderDOMDocument();
    $result = $loader->load(NULL, 'html');
    $this->assertFalse($result);
  }
}

?>