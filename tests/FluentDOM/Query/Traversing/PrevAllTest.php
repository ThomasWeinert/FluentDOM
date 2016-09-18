<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingPrevAllTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::prevAll
     */
    public function testPrevAll() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//div[@id = "start"]')
        ->prev()
        ->addClass('before');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::prevAll
     */
    public function testPrevAllExpression() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//div[@class= "here"]')
        ->prevAll('.//span')
        ->addClass('nextTest');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}