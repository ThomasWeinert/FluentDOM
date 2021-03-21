<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class TraversingFilterTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::filter
     */
    public function testFilter(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $this->assertTrue($fd->length > 1);
      $filterFd = $fd->filter('name() = "items"');
      $this->assertEquals(1, $filterFd->length);
      $this->assertTrue($filterFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::filter
     */
    public function testFilterWithFunction(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $this->assertTrue($fd->length > 1);
      $filterFd = $fd->filter(
        function ($node) {
          return $node->nodeName == "items";
        }
      );
      $this->assertEquals(1, $filterFd->length);
      $this->assertTrue($filterFd !== $fd);
    }
  }
}
