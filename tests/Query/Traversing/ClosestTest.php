<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingClosestTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::closest
     */
    public function testClosest() {
      $attribute = $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->closest('name() = "group"')
        ->attr("id");
      $this->assertEquals('1st', $attribute);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::closest
     */
    public function testClosestIsCurrentNode() {
      $attribute = $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->closest('self::item[@index = "1"]')
        ->attr("index");
      $this->assertEquals('1', $attribute);
    }
  }
}