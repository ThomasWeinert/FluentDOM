<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Utility\Iterators {

  use FluentDOM\DOM\Document;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class ElementIteratorTest extends TestCase {

    /**
     * @covers \FluentDOM\Utility\Iterators\ElementIterator
     */
    public function testIterator(): void {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $this->assertSame(
        [
          $document->documentElement->firstChild,
          $document->documentElement->lastChild,
        ],
        iterator_to_array($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Utility\Iterators\ElementIterator
     */
    public function testSeek(): void {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = $document->documentElement->getIterator();
      $iterator->seek(1);
      $this->assertEquals(
        'two', $iterator->current()->nodeName
      );
    }

    /**
     * @covers \FluentDOM\Utility\Iterators\ElementIterator
     */
    public function testSeekWithInvalidPositionExpectingException(): void {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = $document->documentElement->getIterator();
      $this->expectException(\InvalidArgumentException::class);
      $this->expectExceptionMessage('Unknown position 99, only 2 items');
      $iterator->seek(99);
    }

    /**
     * @covers \FluentDOM\Utility\Iterators\ElementIterator
     */
    public function testRecursiveIterator(): void {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = new \RecursiveIteratorIterator(
        $document->documentElement, \RecursiveIteratorIterator::SELF_FIRST
      );
      $this->assertSame(
        [
          $document->documentElement->firstChild,
          $document->documentElement->lastChild,
          $document->documentElement->lastChild->lastChild
        ],
        iterator_to_array($iterator, FALSE)
      );
    }

    /**
     * @covers \FluentDOM\Utility\Iterators\ElementIterator
     */
    public function testGetChildrenOnTextNodeExpectingException(): void {
      $document = new Document();
      $document->loadXML('<items>ONE<two><three/></two></items>');
      $iterator = $document->documentElement->getIterator();
      $this->expectException(\UnexpectedValueException::class);
      $this->expectExceptionMessage(
        'Called FluentDOM\Utility\Iterators\ElementIterator::getChildren with invalid current element.'
      );
      $iterator->getChildren();
    }

  }
}
