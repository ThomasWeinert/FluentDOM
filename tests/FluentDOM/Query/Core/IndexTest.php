<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingIndexTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Core
     * @covers FluentDOM\Query::index
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
     */
    public function testIndexOnEmptyList() {
      $fd = new Query();
      $this->assertEquals(-1, $fd->index());
    }

    /**
     * @group Core
     * @covers FluentDOM\Query::toArray
     */
    public function testToArray() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $this->assertSame(
        array(
          $fd[0],
          $fd[1]
        ),
        iterator_to_array($fd)
      );
    }
  }
}