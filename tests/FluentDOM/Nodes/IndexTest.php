<?php
namespace FluentDOM {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class NodesIndexTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Nodes
     * @covers FluentDOM\Nodes::index
     */
    public function testIndex() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item[@index >= 1]');
      $this->assertEquals(
        1,
        $fd->index()
      );
    }

    /**
     * @group Nodes
     * @covers FluentDOM\Nodes::index
     */
    public function testIndexWithExpression() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $this->assertEquals(
        1,
        $fd->index('@index = 1')
      );
    }

    /**
     * @group Nodes
     * @covers FluentDOM\Nodes::index
     */
    public function testIndexWithNonMatchingExpression() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $this->assertEquals(
        -1,
        $fd->index('@index = 99')
      );
    }

    /**
     * @group Nodes
     * @covers FluentDOM\Nodes::index
     */
    public function testIndexWithNode() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $node = $fd->get(1);
      $this->assertEquals(
        1,
        $fd->index($node)
      );
    }

    /**
     * @group Nodes
     * @covers FluentDOM\Nodes::index
     */
    public function testIndexOnEmptyList() {
      $fd = new Query();
      $this->assertEquals(-1, $fd->index());
    }
  }
}