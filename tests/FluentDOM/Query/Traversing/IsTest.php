<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class TraversingIsTest extends TestCase {

    protected $_directory = __DIR__;
    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::is
     */
    public function testIs() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $this->assertTrue($fd->length > 1);
      $this->assertTrue($fd->is('name() = "items"'));
      $this->assertFalse($fd->is('name() = "invalidItemName"'));
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::is
     */
    public function testIsOnEmptyList() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $this->assertTrue($fd->length == 0);
      $this->assertFalse($fd->is('name() = "items"'));
    }
  }
}