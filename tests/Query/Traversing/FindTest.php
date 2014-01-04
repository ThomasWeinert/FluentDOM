<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingFindTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     */
    public function testFind() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = $fd->find('group/item');
      $this->assertEquals(3, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     */
    public function testFindFromRootNode() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = $this->getQueryFixtureFromString(self::XML)->find('/items');
      $this->assertEquals(1, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }
  }
}
