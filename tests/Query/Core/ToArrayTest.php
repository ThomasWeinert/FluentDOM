<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingToArrayTest extends TestCase {

    protected $_directory = __DIR__;

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
        $fd->toArray()
      );
    }
  }
}