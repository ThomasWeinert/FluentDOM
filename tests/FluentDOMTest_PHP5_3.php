<?php
/**
*Collection of test for the FluentDOM class supporting PHP5.3
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
require_once dirname(__FILE__).'/../FluentDOM.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOM.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMTest_PHP5_3 extends PHPUnit_Framework_TestCase {

  /**
  * directory of this file
  * @var string
  */
  private $_directory = '';

  /**
  * initialize test suite
  *
  * @access public
  * @return
  */
  function setUp() {
    $this->_directory = dirname(__FILE__);
  }
  
  /**
  *
  * @group TraversingFilter
  */
  function testMap() {
    $this->assertFileExists($this->_directory.'/data/map.src.xml');
    $dom = FluentDOM(file_get_contents($this->_directory.'/data/map.src.xml'));
    $dom->find('//p')
      ->append(
        implode(
          ', ',
          $dom
            ->find('//input')
            ->map(
              function ($node, $index) {
                return FluentDOM($node)->attr("value");
              }
            )
        )
      );
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/map.tgt.xml', $dom);
  }
}
?>