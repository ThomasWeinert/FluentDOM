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

  class EndTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingChaining
     * @covers \FluentDOM\Query::end
     */
    public function testEnd(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/items')->find('.//item');
      $this->assertEquals(3, $fd->length);
      $endFd = $fd->end();
      $this->assertEquals(1, $endFd->length);
      $this->assertTrue($endFd !== $fd);
      $endFdRoot = $endFd->end();
      $this->assertTrue($endFd !== $endFdRoot);
      $endFdRoot2 = $endFdRoot->end();
      $this->assertTrue($endFdRoot === $endFdRoot2);
    }
  }
}
