<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Nodes {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class IndexTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::index
     */
    public function testIndex(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item[@index >= 1]');
      $this->assertEquals(
        1,
        $fd->index()
      );
    }

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::index
     */
    public function testIndexWithExpression(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $this->assertEquals(
        1,
        $fd->index('@index = 1')
      );
    }

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::index
     */
    public function testIndexWithNonMatchingExpression(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $this->assertEquals(
        -1,
        $fd->index('@index = 99')
      );
    }

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::index
     */
    public function testIndexWithNode(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $node = $fd->get(1);
      $this->assertEquals(
        1,
        $fd->index($node)
      );
    }

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::index
     */
    public function testIndexOnEmptyList(): void {
      $fd = new Query();
      $this->assertEquals(-1, $fd->index());
    }
  }
}
