<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingNextAllTest extends TestCase {

    protected $_directory = __DIR__;
    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::nextAll
     */
    public function testNextAll() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//div[position() = 1]')
        ->nextAll()
        ->addClass('after');
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}