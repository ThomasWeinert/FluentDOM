<?php
/**
* Base class for the FluentDOM test cases
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
class FluentDOMTestCase extends PHPUnit_Framework_TestCase {

  /**
  * directory of this file
  * @var string
  *
  * @todo set to private after refactoring to new base class
  */
  protected $_directory = '';

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
  * Tests, if the content of a file equals the given string
  *
  * The the file to be compared is identified by the given function name.
  *
  * @param string $functionName
  * @param string $actual
  *
  * @uses getFileName()
  */
  protected function assertFluentDOMEqualsXMLFile($functionName, $actual) {
    $fileName = $this->getFileName($functionName, 'tgt');
    $this->assertXmlStringEqualsXmlFile($fileName, $actual);
  }

  /**
  *
  * @param string $functionName
  * @return FluentDOM
  */
  protected function getFixtureFromFile($functionName) {
    $fileName = $this->getFileName($functionName, 'src');
    if (!file_exists($fileName)) {
      throw new Exception('File Not Found: '. $fileName);
    }
    $dom = new DOMDocument();
    $dom->load($fileName);
    $loader = $this->getMock('FluentDOMLoader');
    $loader->expects($this->once())
           ->method('load')
           ->with($this->equalTo($fileName))
           ->will($this->returnValue($dom));
    $fd = new FluentDOM();
    $fd->setLoaders(array($loader));
    return $fd->load($fileName);
  }
  
  /**
  * @param string $string
  * @access protected
  * @return FluentDOM
  */
  protected function getFixtureFromString($string) {
    $dom = new DOMDocument();
    $dom->loadXML($string);
    $loader = $this->getMock('FluentDOMLoader');
    $loader->expects($this->once())
           ->method('load')
           ->with($this->equalTo(''))
           ->will($this->returnValue($dom));
    $fd = new FluentDOM();
    $fd->setLoaders(array($loader));
    return $fd->load('');
  }

  /**
  *
  * @param string $functionName
  * @param string $type
  * @return string
  */
  protected function getFileName($functionName, $type) {
    return sprintf('%s/data/%s%s.%s.xml',
      $this->_directory,
      strToLower(substr($functionName, 4, 1)),
      substr($functionName, 5),
      $type
    );
  }
}
?>