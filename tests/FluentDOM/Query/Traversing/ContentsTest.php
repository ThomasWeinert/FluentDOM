<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingContentsTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::contents
     */
    public function testContents() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__)
        ->find('//div[@id = "container"]/p')
        ->contents();
      $this->assertEquals(5, $fd->length);
    }
  }
}