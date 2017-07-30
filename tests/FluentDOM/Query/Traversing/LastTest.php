<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class TraversingLastTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::last
     */
    public function testLast() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $fdFilter = $fd->last();
      $this->assertSame('2', $fdFilter->item(0)->getAttribute('index'));
      $this->assertNotSame($fd, $fdFilter);
    }
  }
}