<?php
namespace FluentDOM\Query\Css {

  use FluentDOM\Query;
  use FluentDOM\Query\Css;
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
    public function testCompare($expected, $nameOne, $nameTwo) {
      $compare = new PropertyCompare();
      $this->assertEquals(
        $expected,
        $compare($nameOne, $nameTwo)
      );
    }

    public static function providePropertyNames() {
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