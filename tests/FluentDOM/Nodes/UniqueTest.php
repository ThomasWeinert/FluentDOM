<?php
namespace FluentDOM {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class NodesUniqueTest extends TestCase {

    /**
     * @group Nodes
     * @covers \FluentDOM\Nodes::unique
     */
    public function testUniqueKeepsAllNodes() {
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