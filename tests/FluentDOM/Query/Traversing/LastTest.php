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

  class LastTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::last
     */
    public function testLast(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $fdFilter = $fd->last();
      $this->assertSame('2', $fdFilter->item(0)->getAttribute('index'));
      $this->assertNotSame($fd, $fdFilter);
    }
  }
}
