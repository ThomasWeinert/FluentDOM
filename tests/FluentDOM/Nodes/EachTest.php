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

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class NodesEachTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Nodes
     * @covers \FluentDOM\Query::each
     */
    public function testEach(): void {
      $data = [];
      $collect = function($node) use (&$data) {
        $data[] = (string)$node;
      };
      $fd = (new Nodes(self::XML))->find('//item|//div/text()');
      $fd->each($collect);
      $this->assertEquals(
        [
          'text1', 'text2', 'text3',
          'class testing', 'class testing', 'class testing'
        ],
        $data
      );
    }

    /**
     * @group Nodes
     * @covers \FluentDOM\Query::each
     */
    public function testEachIgnoringTextNodes(): void {
      $data = [];
      $collect = function($node) use (&$data) {
        $data[] = (string)$node;
      };
      $fd = (new Nodes(self::XML))->find('//item|//div/text()');
      $fd->each($collect, TRUE);
      $this->assertEquals(
        ['text1', 'text2', 'text3'],
        $data
      );
    }

    /**
     * @group Nodes
     * @covers \FluentDOM\Query::each
     */
    public function testEachWithFilterFunction(): void {
      $data = [];
      $collect = function($node) use (&$data) {
        $data[] = (string)$node;
      };
      $fd = (new Nodes(self::XML))->find('//item');
      $fd->each(
        $collect,
        function ($node) {
          return $node['index'] == 2;
        }
      );
      $this->assertEquals(
        ['text3'],
        $data
      );
    }
  }
}
