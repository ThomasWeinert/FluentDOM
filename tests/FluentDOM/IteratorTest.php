<?php
/**
* Collection of test for the FluentDOMIterator class supporting PHP 5.2
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* load necessary files
*/
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../FluentDOM.php';
require_once dirname(__FILE__).'/../../FluentDOM/Iterator.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMIterator.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMIteratorTest extends PHPUnit_Framework_TestCase {

  public function testIteratorCurrent() {
    $fd = $this->getMock('FluentDOM');
    $fd->expects($this->once())
       ->method('item')
       ->with($this->equalTo(0))
       ->will($this->returnValue(TRUE));
    $fdi = new FluentDOMIterator($fd);
    $this->assertTrue($fdi->current());
  }

  public function testIteratorKey() {
    $fd = $this->getMock('FluentDOM');
    $fdi = new FluentDOMIterator($fd);
    $this->assertEquals(0, $fdi->key());
  }

  public function testIteratorNext() {
    $fd = $this->getMock('FluentDOM');
    $fdi = new FluentDOMIterator($fd);
    $this->assertEquals(0, $this->readAttribute($fdi, '_position'));
    $fdi->next();
    $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
  }

  public function testIteratorRewind() {
    $fd = $this->getMock('FluentDOM');
    $fdi = new FluentDOMIterator($fd);
    $fdi->next();
    $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
    $fdi->rewind();
    $this->assertEquals(0, $this->readAttribute($fdi, '_position'));
  }

  public function testIteratorSeek() {
    $fd = $this->getMock('FluentDOM');
    $fd->expects($this->once())
       ->method('count')
       ->will($this->returnValue(2));
    $fdi = new FluentDOMIterator($fd);
    $fdi->seek(1);
    $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
  }

  public function testIteratorSeekToInvalidPosition() {
    try {
      $fd = $this->getMock('FluentDOM');
      $fd->expects($this->once())
         ->method('count')
         ->will($this->returnValue(1));
      $fdi = new FluentDOMIterator($fd);
      $fdi->seek(1);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  public function testIteratorValid() {
    $fd = $this->getMock('FluentDOM');
    $fd->expects($this->once())
       ->method('item')
       ->will($this->returnValue(new stdClass));
    $fdi = new FluentDOMIterator($fd);
    $this->assertTrue($fdi->valid());
  }

  public function testGetChildren() {
    $fd = $this->getMock('FluentDOM');
    $fd->expects($this->once())
       ->method('eq')
       ->with($this->equalTo(0))
       ->will($this->returnValue($fd));
    $fd->expects($this->once())
       ->method('find')
       ->with($this->equalTo('node()'))
       ->will($this->returnValue($fd));
    $fd->expects($this->once())
       ->method('getIterator')
       ->will($this->returnValue(new FluentDOMIterator($fd)));
    $fdi = new FluentDOMIterator($fd);
    $this->assertTrue($fdi->getChildren() instanceof FluentDOMIterator);
  }

  public function testHasChildren() {
    $fd = $this->getMock('FluentDOM');
    $fd->expects($this->once())
       ->method('eq')
       ->with($this->equalTo(0))
       ->will($this->returnValue($fd));
    $fd->expects($this->once())
       ->method('find')
       ->with($this->equalTo('node()'))
       ->will($this->returnValue($fd));
    $fd->expects($this->once())
       ->method('count')
       ->will($this->returnValue(1));
    $fdi = new FluentDOMIterator($fd);
    $this->assertEquals(TRUE, $fdi->hasChildren());
  }
}
?>