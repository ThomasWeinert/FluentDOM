<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingGetTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers FluentDOM\Query::get
     */
    public function testGet() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $this->assertSame(
        array(
          $fd[0],
          $fd[1]
        ),
        $fd->get()
      );
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers FluentDOM\Query::get
     */
    public function testGetWithPosition() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $this->assertSame(
        array(
          $fd[0]
        ),
        $fd->get(0)
      );
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers FluentDOM\Query::get
     */
    public function testGetWithNegativePosition() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $this->assertSame(
        array(
          $fd[0]
        ),
        $fd->get(-2)
      );
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers FluentDOM\Query::get
     */
    public function testGetWithInvalidPosition() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/*');
      $this->assertSame(
        array(),
        $fd->get(99)
      );
    }
  }
}