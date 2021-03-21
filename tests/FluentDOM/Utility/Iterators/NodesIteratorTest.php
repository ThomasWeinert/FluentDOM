<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Utility\Iterators {

  use FluentDOM\DOM\Document;
  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class NodesIteratorTest extends TestCase {

    public function testIteratorCurrent(): void {
      $document = new Document();

      $fd = $this->getMockBuilder(Query::class)->getMock();
      $fd->expects($this->once())
        ->method('item')
        ->with($this->equalTo(0))
        ->will($this->returnValue($document->createElement('test')));
      $fdi = new NodesIterator($fd);
      $this->assertInstanceOf(\DOMNode::class, $fdi->current());
    }

    public function testIteratorKey(): void {
      $fd = $this->getMockBuilder(Query::class)->getMock();
      $fdi = new NodesIterator($fd);
      $this->assertEquals(0, $fdi->key());
    }

    public function testIteratorNext(): void {
      $fd = $this->getMockBuilder(Query::class)->getMock();
      $fdi = new NodesIterator($fd);
      $this->assertEquals(0, $fdi->key());
      $fdi->next();
      $this->assertEquals(1, $fdi->key());
    }

    public function testIteratorRewind(): void {
      $fd = $this->getMockBuilder(Query::class)->getMock();
      $fdi = new NodesIterator($fd);
      $fdi->next();
      $this->assertEquals(1, $fdi->key());
      $fdi->rewind();
      $this->assertEquals(0, $fdi->key());
    }

    public function testIteratorSeek(): void {
      $fd = $this->getMockBuilder(Query::class)->getMock();
      $fd->expects($this->once())
        ->method('count')
        ->will($this->returnValue(2));
      $fdi = new NodesIterator($fd);
      $fdi->seek(1);
      $this->assertEquals(1, $fdi->key());
    }

    public function testIteratorSeekToInvalidPosition(): void {
      $fd = $this->getMockBuilder(Query::class)->getMock();
      $fd->expects($this->exactly(2))
        ->method('count')
        ->will($this->returnValue(1));
      $fdi = new NodesIterator($fd);
      $this->expectException(
        \InvalidArgumentException::class,
        'Unknown position 1, only 1 items'
      );
      $fdi->seek(1);
    }

    public function testIteratorValid(): void {
      $fd = $this->getMockBuilder(Query::class)->getMock();
      $fd->expects($this->once())
        ->method('item')
        ->will($this->returnValue(new \stdClass));
      $fdi = new NodesIterator($fd);
      $this->assertTrue($fdi->valid());
    }

    public function testGetChildren(): void {
      $fdSource = $this->getMockBuilder(Query::class)->getMock();
      $fdSpawn = $this->getMockBuilder(Query::class)->getMock();
      $document = new \DOMDocument();
      $node = $document->creatEelement('parent');
      $node->appendChild($document->createElement('child'));
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
        ->with($this->isInstanceOf(\DOMNodeList::class));
      $fdi = new NodesIterator($fdSource);
      $this->assertInstanceOf(
        NodesIterator::class,
        $fdi->getChildren()
      );
    }

    public function testHasChildren(): void {
      $fd = $this->getMockBuilder(Query::class)->getMock();
      $node = $this
        ->getMockBuilder(\DOMElement::class)
        ->setConstructorArgs(['dummy'])
        ->getMock();
      $fd
        ->expects($this->once())
        ->method('item')
        ->with($this->equalTo(0))
        ->will($this->returnValue($node));
      $node
        ->expects($this->once())
        ->method('hasChildNodes')
        ->will($this->returnValue(TRUE));
      $fdi = new NodesIterator($fd);
      $this->assertTrue($fdi->hasChildren());
    }
  }
}
