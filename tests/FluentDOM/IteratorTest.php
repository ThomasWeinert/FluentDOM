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
require_once (dirname(__FILE__).'/../FluentDOMTestCase.php');

/**
* Test class for FluentDOMIterator.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMIteratorTest extends FluentDOMTestCase {

  public function testIteratorCurrent() {
    $fd = $this->getMock('FluentDOMCore');
    $fd->expects($this->once())
       ->method('item')
       ->with($this->equalTo(0))
       ->will($this->returnValue(TRUE));
    $fdi = new FluentDOMIterator($fd);
    $this->assertTrue($fdi->current());
  }

  public function testIteratorKey() {
    $fd = $this->getMock('FluentDOMCore');
    $fdi = new FluentDOMIterator($fd);
    $this->assertEquals(0, $fdi->key());
  }

  public function testIteratorNext() {
    $fd = $this->getMock('FluentDOMCore');
    $fdi = new FluentDOMIterator($fd);
    $this->assertEquals(0, $this->readAttribute($fdi, '_position'));
    $fdi->next();
    $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
  }

  public function testIteratorRewind() {
    $fd = $this->getMock('FluentDOMCore');
    $fdi = new FluentDOMIterator($fd);
    $fdi->next();
    $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
    $fdi->rewind();
    $this->assertEquals(0, $this->readAttribute($fdi, '_position'));
  }

  public function testIteratorSeek() {
    $fd = $this->getMock('FluentDOMCore');
    $fd->expects($this->once())
       ->method('count')
       ->will($this->returnValue(2));
    $fdi = new FluentDOMIterator($fd);
    $fdi->seek(1);
    $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
  }

  public function testIteratorSeekToInvalidPosition() {
    try {
      $fd = $this->getMock('FluentDOMCore');
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
    $fd = $this->getMock('FluentDOMCore');
    $fd->expects($this->once())
       ->method('item')
       ->will($this->returnValue(new stdClass));
    $fdi = new FluentDOMIterator($fd);
    $this->assertTrue($fdi->valid());
  }

  public function testGetChildren() {
    $fdSource = $this->getMock('FluentDOMCore');
    $fdSpawn = $this->getMock('FluentDOMCore');
    $dom = new DOMDocument();
    $node = $dom->creatEelement('parent');
    $node->appendChild($dom->createElement('child'));
    $fdSource
      ->expects($this->once())
      ->method('spawn')
      ->will($this->returnValue($fdSpawn));
    $fdSource
      ->expects($this->once())
      ->method('item')
      ->with($this->equalTo(0))
      ->will($this->returnValue($node));
    $fdSpawn
      ->expects($this->once())
      ->method('push')
      ->with($this->isInstanceOf('DOMNodeList'));
    $fdi = new FluentDOMIterator($fdSource);
    $this->assertInstanceOf(
      'FluentDOMIterator',
      $fdi->getChildren()
    );
  }

  public function testHasChildren() {
    $fd = $this->getMock('FluentDOMCore');
    $node = $this->getMock('DOMElement', array(), array('dummy'));
    $fd
      ->expects($this->once())
      ->method('item')
      ->with($this->equalTo(0))
      ->will($this->returnValue($node));
    $node
      ->expects($this->once())
      ->method('hasChildNodes')
      ->will($this->returnValue(TRUE));
    $fdi = new FluentDOMIterator($fd);
    $this->assertTrue($fdi->hasChildren());
  }
}
