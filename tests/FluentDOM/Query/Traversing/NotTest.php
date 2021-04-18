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

  class NotTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::not
     */
    public function testNot(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $this->assertTrue($fd->length > 1);
      $notDoc = $fd->not('name() != "items"');
      $this->assertEquals(1, $notDoc->length);
      $this->assertTrue($notDoc !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers \FluentDOM\Query::not
     */
    public function testNotWithFunction(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//*');
      $this->assertTrue($fd->length > 1);
      $notDoc = $fd->not(
        function (\DOMNode $node) {
          return $node->nodeName !== "items";
        }
      );
      $this->assertEquals(1, $notDoc->length);
      $this->assertTrue($notDoc !== $fd);
    }
  }
}
