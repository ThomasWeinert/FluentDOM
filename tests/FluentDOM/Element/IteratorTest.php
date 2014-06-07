<?php
namespace FluentDOM\Element {

  use FluentDOM\Element;
  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class IteratorTest extends TestCase {

    /**
     * @covers FluentDOM\Element\Iterator
     */
    public function testIterator() {
      $dom = new Document();
      $dom->loadXML('<items>ONE<two><three/></two></items>');
      $this->assertEquals(
        array(
          $dom->documentElement->firstChild,
          $dom->documentElement->lastChild,
        ),
        iterator_to_array($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Element\Iterator
     */
    public function testRecursiveIterator() {
      $dom = new Document();
      $dom->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = new \RecursiveIteratorIterator(
        $dom->documentElement, \RecursiveIteratorIterator::SELF_FIRST
      );
      $this->assertEquals(
        array(
          $dom->documentElement->firstChild,
          $dom->documentElement->lastChild->firstChild,
          $dom->documentElement->lastChild->lastChild
        ),
        iterator_to_array($iterator, FALSE)
      );
    }

  }
}