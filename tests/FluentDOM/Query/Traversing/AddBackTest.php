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

  class AddBackTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingChaining
     * @covers \FluentDOM\Query::addBack
     */
    public function testAddBack(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items')->find('.//item');
      $this->assertEquals(3, $fd->length);
      $addBackFd = $fd->addBack();
      $this->assertEquals(4, $addBackFd->length);
      $this->assertTrue($addBackFd !== $fd);
    }
  }
}
