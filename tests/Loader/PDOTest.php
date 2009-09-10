<?php
/**
* DOMDocument loader test for FluentDOMLoaderPDO
*
* @version $Id: DOMNodeTest.php 313 2009-08-19 12:01:49Z subjective $
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
require_once dirname(__FILE__).'/../../Loader/PDO.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderPDO.
*
* @package FluentDOM
* @subpackage UnitTests
*/
class FluentDOMLoaderPDOTest extends PHPUnit_Framework_TestCase {

  public function testLoad() {
    $loader = new FluentDOMLoaderPDO();
    $database = new PDO('sqlite:'.dirname(__FILE__).'/data/FluentDOMLoaderPDO.sqlite');
    $statment = $database->query('SELECT * FROM sample');
    $result = $loader->load($statment, 'text/xml');
    $this->assertTrue($result instanceof DOMDocument);
    $this->assertXmlStringEqualsXmlFile(
      dirname(__FILE__).'/data/FluentDOMLoaderPDO.xml',
      $result->saveXML()
    );
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderDOMNode();
    $result = $loader->load(NULL, 'text/xml');
    $this->assertFalse($result);
  }
}

?>