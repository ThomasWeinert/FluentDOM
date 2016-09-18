<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingPrevTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::prev
     */
    public function testPrev() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd ->find('//div[@id = "start"]')
        ->prev()
        ->addClass('before');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::prev
     */
    public function testPrevExpression() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//div[@class = "here"]')
        ->prev()
        ->addClass('nextTest');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}