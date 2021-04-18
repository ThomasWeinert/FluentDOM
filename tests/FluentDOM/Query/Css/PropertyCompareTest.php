<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query\Css {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class PropertyCompareTest extends TestCase {

    /**
     * @covers \FluentDOM\Query\Css\PropertyCompare
     * @dataProvider providePropertyNames
     * @param int $expected
     * @param string $nameOne
     * @param string $nameTwo
     */
    public function testCompare(int $expected, string $nameOne, string $nameTwo): void {
      $compare = new PropertyCompare();
      $this->assertEquals(
        $expected,
        $compare($nameOne, $nameTwo)
      );
    }

    public static function providePropertyNames(): array {
      return [
        [-1, 'margin', 'margin-top'],
        [1, 'padding', 'margin-top'],
        [-1, 'margin-top', 'padding'],
        [0, 'padding', 'padding'],
        [1, '-moz-box-sizing', 'box-sizing'],
        [1, '-padding-top', 'margin-top'],
        [1, '-padding-top', 'padding-bottom'],
      ];
    }
  }
}
