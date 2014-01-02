<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingReverseTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingChaining
     * @covers FluentDOM\Query::reverse
     */
    public function testReverse() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item')->reverse();
      $this->assertEquals(2, $fd[0]->getAttribute('index'));
    }
  }
}