<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class TraversingFirstTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::first
     */
    public function testFirst() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $fdFilter = $fd->first();
      $this->assertSame('0', $fdFilter->item(0)->getAttribute('index'));
      $this->assertNotSame($fd, $fdFilter);
    }
  }
}