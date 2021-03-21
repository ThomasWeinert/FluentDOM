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

  class NodesUniqueTest extends TestCase {

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::unique
     */
    public function testUniqueKeepsAllNodes(): void {
      $fd = new Nodes(
        '<root><items><one/><two/><three/></items><items><one/><two/><three/></items></root>'
      );
      $all =  iterator_to_array($fd->xpath->evaluate('//*'));
      $this->assertSame(
        $all,
        $fd->unique($all)
      );
    }
  }
}
