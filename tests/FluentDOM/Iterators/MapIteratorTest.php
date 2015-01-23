<?php
namespace FluentDOM\Iterators {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class MapIteratorTest extends TestCase {

    /**
     * @covers FluentDOM\Iterators\MapIterator
     */
    public function testIterator() {
      $iterator = new MapIterator(
        new \ArrayIterator(['one', 'two']),
        function ($value, $index) {
          return [$index, $value];
        }
      );
      $this->assertEquals(
        [
          [0, 'one'],
          [1, 'two']
        ],
        iterator_to_array($iterator)
      );
    }
  }
}
