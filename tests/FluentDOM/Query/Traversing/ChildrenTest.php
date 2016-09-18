<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingChildrenTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::children
     */
    public function testChildren() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__)
        ->find('//div[@id = "container"]/p')
        ->children();
      $this->assertEquals(2, $fd->length);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::children
     */
    public function testChildrenExpression() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__)
        ->find('//div[@id = "container"]/p')
        ->children('name() = "em"');
      $this->assertEquals(1, $fd->length);
    }
  }
}