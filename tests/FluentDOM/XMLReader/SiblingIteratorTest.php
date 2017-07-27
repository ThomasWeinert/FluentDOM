<?php
namespace FluentDOM\XMLReader {

  use FluentDOM\TestCase;
  use FluentDOM\XMLReader;

  require_once(__DIR__ . '/../TestCase.php');

  class SiblingIteratorTest extends TestCase {

    /**
     * @covers \FluentDOM\XMLReader\SiblingIterator
     */
    public function testIteration() {
      $reader = new XMLReader();
      $reader->open(__DIR__.'/../TestData/xmlreader-1.xml');
      $result = [];
      foreach (new SiblingIterator($reader, 'child') as $child) {
        $result[] = $child['name'];
      }

      $this->assertEquals(
        ['one', 'two', 'three'], $result
      );
    }
  }
}