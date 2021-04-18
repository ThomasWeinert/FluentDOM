<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query\Traversing {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class EqTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::eq
     */
    public function testEq(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $eqFd = $fd->eq(0);
      $this->assertSame(
        [$fd[0]],
        iterator_to_array($eqFd)
      );
      $this->assertTrue($eqFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::eq
     */
    public function testEqWithNegativeOffset(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $eqFd = $fd->eq(-2);
      $this->assertSame(
        [$fd[0]],
        iterator_to_array($eqFd)
      );
      $this->assertTrue($eqFd !== $fd);
    }
  }
}
