<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query\Manipulation {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class CloneTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationCopy
     * @covers \FluentDOM\Query
     */
    public function testClone(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $clonedNodes = $fd->clone();
      $this->assertTrue($clonedNodes instanceof Query);
      $this->assertTrue($fd[0] !== $clonedNodes[0]);
      $this->assertEquals($fd[0]->nodeName, $clonedNodes[0]->nodeName);
      $this->assertEquals($fd[1]->getAttribute('index'), $clonedNodes[1]->getAttribute('index'));
      $this->assertSameSize($fd, $clonedNodes);
    }
  }
}
