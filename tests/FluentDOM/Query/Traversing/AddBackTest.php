<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingAddBackTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingChaining
     * @covers \FluentDOM\Query::addBack
     */
    public function testAddBack() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items')->find('.//item');
      $this->assertEquals(3, $fd->length);
      $addBackFd = $fd->addBack();
      $this->assertEquals(4, $addBackFd->length);
      $this->assertTrue($addBackFd !== $fd);
    }
  }
}