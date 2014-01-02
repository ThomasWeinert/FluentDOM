<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingEqTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers FluentDOM\Query::eq
     */
    public function testEq() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $eqFd = $fd->eq(0);
      $this->assertSame(
        array(
          $fd[0]
        ),
        iterator_to_array($eqFd)
      );
      $this->assertTrue($eqFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers FluentDOM\Query::eq
     */
    public function testEqWithNegativeOffset() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $eqFd = $fd->eq(-2);
      $this->assertSame(
        array(
          $fd[0]
        ),
        iterator_to_array($eqFd)
      );
      $this->assertTrue($eqFd !== $fd);
    }
  }
}