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

  class GetTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::get
     */
    public function testGet(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $this->assertSame(
        [
          $fd[0],
          $fd[1]
        ],
        $fd->get()
      );
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::get
     */
    public function testGetWithPosition(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $this->assertSame(
        $fd[0],
        $fd->get(0)
      );
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::get
     */
    public function testGetWithNegativePosition(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items/*');
      $this->assertSame(
        $fd[0],
        $fd->get(-2)
      );
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::get
     */
    public function testGetWithInvalidPosition(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/*');
      $this->assertNull(
        $fd->get(99)
      );
    }
  }
}
