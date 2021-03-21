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

  class TraversingReverseTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingChaining
     * @covers \FluentDOM\Query::reverse
     */
    public function testReverse(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item')->reverse();
      $this->assertEquals(2, $fd[0]->getAttribute('index'));
    }
  }
}
