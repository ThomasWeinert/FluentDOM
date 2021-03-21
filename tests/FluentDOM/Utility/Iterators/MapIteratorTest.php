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

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class MapIteratorTest extends TestCase {

    /**
     * @covers \FluentDOM\Utility\Iterators\MapIterator
     */
    public function testIterator(): void {
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
