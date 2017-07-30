<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class ManipulationCloneTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationCopy
     * @covers \FluentDOM\Query
     */
    public function testClone() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $clonedNodes = $fd->clone();
      $this->assertTrue($clonedNodes instanceof Query);
      $this->assertTrue($fd[0] !== $clonedNodes[0]);
      $this->assertEquals($fd[0]->nodeName, $clonedNodes[0]->nodeName);
      $this->assertEquals($fd[1]->getAttribute('index'), $clonedNodes[1]->getAttribute('index'));
      $this->assertEquals(count($fd), count($clonedNodes));
    }
  }
}