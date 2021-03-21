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

  class TraversingClosestTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::closest
     */
    public function testClosest(): void {
      $attribute = $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->closest('name() = "group"')
        ->attr("id");
      $this->assertEquals('1st', $attribute);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::closest
     */
    public function testClosestWithContext(): void {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $context =  $fd
        ->find('//item');
      $attribute = $fd
        ->find('//item')
        ->closest('name() = "group"', $context)
        ->attr("id");
      $this->assertEquals('1st', $attribute);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::closest
     */
    public function testClosestWithContextExpectingNull(): void {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $context =  $fd
        ->find('//div');
      $attribute = $fd
        ->find('//item')
        ->closest('name() = "group"', $context)
        ->attr("id");
      $this->assertNull($attribute);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::closest
     */
    public function testClosestIsCurrentNode(): void {
      $attribute = $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->closest('self::item[@index = "1"]')
        ->attr("index");
      $this->assertEquals('1', $attribute);
    }
  }
}
