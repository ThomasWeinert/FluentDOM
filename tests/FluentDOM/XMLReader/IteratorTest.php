<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\XMLReader {

  use FluentDOM\TestCase;
  use FluentDOM\XMLReader;

  require_once __DIR__ . '/../TestCase.php';

  class IteratorTest extends TestCase {

    /**
     * @covers \FluentDOM\XMLReader\Iterator
     */
    public function testIteration(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/../TestData/xmlreader-1.xml');
      $result = [];
      foreach (new Iterator($reader, 'child') as $key => $child) {
        $result[$key] = $child['name'];
      }

      $this->assertEquals(
        ['one', 'one.one', 'two', 'three'], $result
      );
    }

    /**
     * @covers \FluentDOM\XMLReader\Iterator
     */
    public function testIterationFailsOnRewind(): void {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/../TestData/xmlreader-1.xml');
      $iterator = new Iterator($reader, 'child');
      iterator_to_array($iterator);
      $this->expectException(\LogicException::class, 'FluentDOM\XMLReader\Iterator is not a seekable iterator');
      $iterator->rewind();
    }
  }
}
