<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class NodesToArrayTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::toArray
     */
    public function testToArray(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $this->assertSame(
        [$fd[0], $fd[1]],
        $fd->toArray()
      );
    }
  }
}
