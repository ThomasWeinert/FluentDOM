<?php
namespace FluentDOM\Query\Css {

  use FluentDOM\Query;
  use FluentDOM\Query\Css;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

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
      return array(
        array(-1, 'margin', 'margin-top'),
        array(1, 'padding', 'margin-top'),
        array(-1, 'margin-top', 'padding'),
        array(0, 'padding', 'padding'),
        array(1, '-moz-box-sizing', 'box-sizing'),
        array(1, '-padding-top', 'margin-top'),
        array(1, '-padding-top', 'padding-bottom'),
      );
    }
  }
}