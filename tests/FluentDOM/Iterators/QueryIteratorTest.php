<?php
namespace FluentDOM\Iterators {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class QueryIteratorTest extends TestCase {

    public function testIteratorCurrent() {
      $fd = $this->getMock('FluentDOM\\Query');
      $fd->expects($this->once())
        ->method('item')
        ->with($this->equalTo(0))
        ->will($this->returnValue(TRUE));
      $fdi = new QueryIterator($fd);
      $this->assertTrue($fdi->current());
    }

    public function testIteratorKey() {
      $fd = $this->getMock('FluentDOM\\Query');
      $fdi = new QueryIterator($fd);
      $this->assertEquals(0, $fdi->key());
    }

    public function testIteratorNext() {
      $fd = $this->getMock('FluentDOM\\Query');
      $fdi = new QueryIterator($fd);
      $this->assertEquals(0, $this->readAttribute($fdi, '_position'));
      $fdi->next();
      $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
    }

    public function testIteratorRewind() {
      $fd = $this->getMock('FluentDOM\\Query');
      $fdi = new QueryIterator($fd);
      $fdi->next();
      $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
      $fdi->rewind();
      $this->assertEquals(0, $this->readAttribute($fdi, '_position'));
    }

    public function testIteratorSeek() {
      $fd = $this->getMock('FluentDOM\\Query');
      $fd->expects($this->once())
        ->method('count')
        ->will($this->returnValue(2));
      $fdi = new QueryIterator($fd);
      $fdi->seek(1);
      $this->assertEquals(1, $this->readAttribute($fdi, '_position'));
    }

    public function testIteratorSeekToInvalidPosition() {
      $fd = $this->getMock('FluentDOM\\Query');
      $fd->expects($this->exactly(2))
        ->method('count')
        ->will($this->returnValue(1));
      $fdi = new QueryIterator($fd);
      $this->setExpectedException(
        'InvalidArgumentException',
        'Unknown position 1, only 1 items'
      );
      $fdi->seek(1);
    }

    public function testIteratorValid() {
      $fd = $this->getMock('FluentDOM\\Query');
      $fd->expects($this->once())
        ->method('item')
        ->will($this->returnValue(new \stdClass));
      $fdi = new QueryIterator($fd);
      $this->assertTrue($fdi->valid());
    }

    public function testGetChildren() {
      $fdSource = $this->getMock('FluentDOM\\Query');
      $fdSpawn = $this->getMock('FluentDOM\\Query');
      $dom = new \DOMDocument();
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
      $fdi = new QueryIterator($fdSource);
      $this->assertInstanceOf(
        'FluentDOM\\Iterators\\QueryIterator',
        $fdi->getChildren()
      );
    }

    public function testHasChildren() {
      $fd = $this->getMock('FluentDOM\\Query');
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
      $fdi = new QueryIterator($fd);
      $this->assertTrue($fdi->hasChildren());
    }
  }
}