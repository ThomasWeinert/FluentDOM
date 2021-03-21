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

  use FluentDOM\DOM\Element;
  use FluentDOM\TestCase;
  use FluentDOM\XMLReader;

  require_once __DIR__ . '/../TestCase.php';

  class SiblingIteratorTest extends TestCase {

    /**
     * @covers \FluentDOM\XMLReader\SiblingIterator
     */
    public function testIteration(): void {
      $reader = new XMLReader();
      /** @noinspection StaticInvocationViaThisInspection */
      $reader->open(
        __DIR__.'/../TestData/xmlreader-1.xml'
      );
      $result = [];
      /**
       * @var XMLReader $reader $key
       * @var Element $child
       */
      foreach (new SiblingIterator($reader, 'child') as $child) {
        $result[] = $child['name'];
      }
      $this->assertEquals(
        ['one', 'two', 'three'], $result
      );
    }
  }
}
