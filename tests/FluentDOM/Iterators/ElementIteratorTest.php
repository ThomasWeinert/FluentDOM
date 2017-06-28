<?php
namespace FluentDOM\Element {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class IteratorTest extends TestCase {

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testIterator() {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $this->assertSame(
        array(
          $document->documentElement->firstChild,
          $document->documentElement->lastChild,
        ),
        iterator_to_array($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testSeek() {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = $document->documentElement->getIterator();
      $iterator->seek(1);
      $this->assertEquals(
        'two', $iterator->current()->nodeName
      );
    }

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testSeekWithInvalidPositionExpectingException() {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = $document->documentElement->getIterator();
      $this->expectException(
        \InvalidArgumentException::class,
        'Unknown position 99, only 2 items'
      );
      $iterator->seek(99);
    }

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testRecursiveIterator() {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = new \RecursiveIteratorIterator(
        $document->documentElement, \RecursiveIteratorIterator::SELF_FIRST
      );
      $this->assertSame(
        array(
          $document->documentElement->firstChild,
          $document->documentElement->lastChild,
          $document->documentElement->lastChild->lastChild
        ),
        iterator_to_array($iterator, FALSE)
      );
    }

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testGetChildrenOnTextNodeExpectingException() {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = $document->documentElement->getIterator();
      $this->expectException(
        \UnexpectedValueException::class,
        'Called FluentDOM\Iterators\ElementIterator::getChildren with invalid current element.'
      );
      $iterator->getChildren();
    }

  }
}