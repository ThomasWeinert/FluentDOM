<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class CoreIndexTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Core
     * @covers FluentDOM\Query::index
     * @covers FluentDOM\Query::getNodes
     * @covers FluentDOM\Query::getContentElement
     */
    public function testIndex() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item[@index >= 1]');
      $this->assertEquals(
        1,
        $fd->index()
      );
    }

    /**
     * @group Core
     * @covers FluentDOM\Query::index
     * @covers FluentDOM\Query::getNodes
     * @covers FluentDOM\Query::getContentElement
     */
    public function testIndexWithExpression() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $this->assertEquals(
        1,
        $fd->index('@index = 1')
      );
    }

    /**
     * @group Core
     * @covers FluentDOM\Query::index
     * @covers FluentDOM\Query::getNodes
     * @covers FluentDOM\Query::getContentElement
     */
    public function testIndexWithNonMatchingExpression() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $this->assertEquals(
        -1,
        $fd->index('@index = 99')
      );
    }

    /**
     * @group Core
     * @covers FluentDOM\Query::index
     * @covers FluentDOM\Query::getNodes
     * @covers FluentDOM\Query::getContentElement
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
     * @group Core
     * @covers FluentDOM\Query::index
     * @covers FluentDOM\Query::getNodes
     * @covers FluentDOM\Query::getContentElement
     */
    public function testIndexOnEmptyList() {
      $fd = new Query();
      $this->assertEquals(-1, $fd->index());
    }
  }
}