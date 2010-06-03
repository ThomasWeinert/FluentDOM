<?php
/**
* DOMDocument loader test for FluentDOMLoaderPDO
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
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Loader/PDO.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderPDO.
*
* @package FluentDOM
* @subpackage UnitTests
*/
class FluentDOMLoaderPDOTest extends FluentDOMTestCase {

  public function testLoad() {
    if (!extension_loaded('pdo')) {
  	  $this->markTestSkipped('PDO extension not loaded');
  	}
  	if (!in_array('sqlite', PDO::getAvailableDrivers())) {
  	  $this->markTestSkipped('PDO SQLite driver not loaded');
  	}
    $loader = new FluentDOMLoaderPDO();
    $database = new PDO('sqlite:'.dirname(__FILE__).'/data/FluentDOMLoaderPDO.sqlite');
    $statement = $database->query('SELECT * FROM sample');
    $contentType = 'text/xml';
    $result = $loader->load($statement, $contentType);
    $this->assertTrue($result instanceof DOMDocument);
    $this->assertXmlStringEqualsXmlFile(
      dirname(__FILE__).'/data/FluentDOMLoaderPDO.xml',
      $result->saveXML()
    );
  }

  public function testLoadInvalid() {
    $loader = new FluentDOMLoaderPDO();
    $contentType = 'text/xml';
    $result = $loader->load(NULL, $contentType);
    $this->assertFalse($result);
  }

  public function testSetTagNames() {
    $loader = new FluentDOMLoaderPDO();
    $loader->setTagNames('samples', 'sample');
    $this->assertSame('samples', $this->readAttribute($loader, '_tagNameRoot'));
    $this->assertSame('sample', $this->readAttribute($loader, '_tagNameRecord'));
  }
}

?>