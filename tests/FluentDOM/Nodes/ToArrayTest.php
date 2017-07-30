<?php
namespace FluentDOM {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class NodesToArrayTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::toArray
     */
    public function testToArray() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $this->assertSame(
        [$fd[0], $fd[1]],
        $fd->toArray()
      );
    }
  }
}