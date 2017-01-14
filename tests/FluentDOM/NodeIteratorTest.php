<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class NodeIteratorTest extends TestCase {

    const W3C_XML = '<xml><A/><B/><C/><D/><E/><F/><G/><H/><I/></xml>';

    public function testNextNodeWithSiblings() {
      $filter = $this->getMockBuilder(NodeFilter::class)->getMock();
      $filter
        ->expects($this->any())
        ->method('acceptNode')
        ->with($this->isInstanceOf(\DOMNode::class))
        ->willReturn(NodeFilter::FILTER_ACCEPT);

      $document = new Document();
      $document->loadXml(self::W3C_XML);
      $iterator = new NodeIterator($document->documentElement, 0, $filter);
      $this->assertEquals('A', $iterator->nextNode()->nodeName);
      $this->assertEquals('B', $iterator->nextNode()->nodeName);
    }

    public function testPreviousNodeWithSiblings() {
      $filter = $this->getMockBuilder(NodeFilter::class)->getMock();
      $filter
        ->expects($this->any())
        ->method('acceptNode')
        ->with($this->isInstanceOf(\DOMNode::class))
        ->willReturn(NodeFilter::FILTER_ACCEPT);

      $document = new Document();
      $document->loadXml(self::W3C_XML);
      $iterator = new NodeIterator($document->documentElement, 0, $filter);
      for ($i = 0; $i < $document->documentElement->childNodes->length; $i++) {
        $iterator->nextNode();
      }
      $this->assertEquals('I', $iterator->previousNode()->nodeName);
      $this->assertEquals('H', $iterator->previousNode()->nodeName);
    }
  }
}


