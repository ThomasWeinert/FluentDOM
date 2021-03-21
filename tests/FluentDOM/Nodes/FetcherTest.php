<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Nodes {

  use FluentDOM\Nodes;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class FetcherTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchDocumentNode(): void {
      $fd = new Nodes(self::XML);
      $fetcher = new Fetcher($fd);
      $this->assertEquals(
        [$fd->document->documentElement],
        $fetcher->fetch(
          '/*',
          NULL,
          NULL,
          Fetcher::IGNORE_CONTEXT
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchWithFilter(): void {
      $fd = (new Nodes(self::XML))->find('/items/group');
      $fetcher = new Fetcher($fd);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<item index="1">text2</item>'],
        $fetcher->fetch(
          'item',
          $fd->getSelectorCallback('@index = 1'),
          NULL
        )
      );
    }


    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchWithContext(): void {
      $fd = (new Nodes(self::XML))->find('/items/group/item');
      $fetcher = new Fetcher($fd);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<item index="1">text2</item>'],
        $fetcher->fetch(
          'self::item[@index = 1]'
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchReverse(): void {
      $fd = (new Nodes(self::XML))->find('/items/group');
      $fetcher = new Fetcher($fd);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        [
          '<item index="2">text3</item>',
          '<item index="1">text2</item>',
          '<item index="0">text1</item>'
        ],
        $fetcher->fetch(
          'item',
          NULL,
          NULL,
          Fetcher::REVERSE
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchUnique(): void {
      $fd = (new Nodes(self::XML))->find('/items/group/item');
      $fetcher = new Fetcher($fd);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        [
          '<item index="0">text1</item>',
          '<item index="1">text2</item>',
          '<item index="2">text3</item>'
        ],
        $fetcher->fetch('self::*', NULL, NULL, Fetcher::UNIQUE)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchUntil(): void {
      $fd = (new Nodes(self::XML))->find('/items/group');
      $fetcher = new Fetcher($fd);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<item index="0">text1</item>'],
        $fetcher->fetch(
          'item',
          NULL,
          $fd->getSelectorCallback('@index = 1')
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchUntilIncludingStop(): void {
      $fd = (new Nodes(self::XML))->find('/items/group');
      $fetcher = new Fetcher($fd);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        [
          '<item index="0">text1</item>',
          '<item index="1">text2</item>'
        ],
        $fetcher->fetch(
          'item',
          NULL,
          $fd->getSelectorCallback('@index = 1'),
          Fetcher::INCLUDE_STOP
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchUntilWithFilter(): void {
      $fd = (new Nodes(self::XML))->find('/items/group');
      $fetcher = new Fetcher($fd);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<item index="1">text2</item>'],
        $fetcher->fetch(
          'item',
          $fd->getSelectorCallback('@index = 1'),
          $fd->getSelectorCallback('@index = 2'),
          Fetcher::INCLUDE_STOP
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchWithInvalidExpressionExpectingException(): void {
      $fd = (new Nodes(self::XML))->find('/items/group');
      $fetcher = new Fetcher($fd);
      $this->expectException(
        \InvalidArgumentException::class,
        'Invalid selector/expression.'
      );
      $fetcher->fetch('');
    }

    /**
     * @covers \FluentDOM\Nodes\Fetcher
     */
    public function testFetchWithScalarExpressionExpectingException(): void {
      $fd = (new Nodes(self::XML))->find('/items/group');
      $fetcher = new Fetcher($fd);
      $this->expectException(
        \InvalidArgumentException::class,
        'Given selector/expression did not return a node list.'
      );
      $fetcher->fetch('count(*)');
    }

    public function assertXmlNodesArrayEqualsXmlStrings($expected, $nodes) {
      $actual = [];
      foreach ($nodes as $node) {
        $actual[] = $node->ownerDocument->saveXml($node);
      }
      $this->assertEquals($expected, $actual);
    }
  }
}
