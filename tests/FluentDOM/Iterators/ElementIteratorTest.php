<?php
namespace FluentDOM\Element {

  use FluentDOM\Element;
  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class IteratorTest extends TestCase {

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testIterator() {
      $dom = new Document();
      $dom->loadXML('<items>ONE<two><three/></two></items>');
      $this->assertSame(
        array(
          $dom->documentElement->firstChild,
          $dom->documentElement->lastChild,
        ),
        iterator_to_array($dom->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testSeek() {
      $dom = new Document();
      $dom->loadXML('<items>ONE<two><three/></two></items>');
      /** @var Iterator $iterator */
      $iterator = $dom->documentElement->getIterator();
      $iterator->seek(1);
      $this->assertEquals(
        'two', $iterator->current()->nodeName
      );
    }

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testSeekWithInvalidPositionExpectingException() {
      $dom = new Document();
      $dom->loadXML('<items>ONE<two><three/></two></items>');
      /** @var Iterator $iterator */
      $iterator = $dom->documentElement->getIterator();
      $this->setExpectedException(
        'InvalidArgumentException',
        'Unknown position 99, only 2 items'
      );
      $iterator->seek(99);
    }

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testRecursiveIterator() {
      $dom = new Document();
      $dom->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = new \RecursiveIteratorIterator(
        $dom->documentElement, \RecursiveIteratorIterator::SELF_FIRST
      );
      $this->assertSame(
        array(
          $dom->documentElement->firstChild,
          $dom->documentElement->lastChild,
          $dom->documentElement->lastChild->lastChild
        ),
        iterator_to_array($iterator, FALSE)
      );
    }

    /**
     * @covers \FluentDOM\Iterators\ElementIterator
     */
    public function testGetChildrenOnTextNodeExpectingException() {
      $dom = new Document();
      $dom->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = $dom->documentElement->getIterator();
      $this->setExpectedException(
        'UnexpectedValueException',
        'Called FluentDOM\Iterators\ElementIterator::getChildren with invalid current element.'
      );
      $iterator->getChildren();
    }

  }
}