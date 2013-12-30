<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingAddSelfTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingChaining
     * @covers FluentDOM\Query::andSelf
     */
    public function testAndSelf() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items')->find('.//item');
      $this->assertEquals(3, $fd->length);
      $andSelfFd = $fd->andSelf();
      $this->assertEquals(4, $andSelfFd->length);
      $this->assertTrue($andSelfFd !== $fd);
    }
  }
}