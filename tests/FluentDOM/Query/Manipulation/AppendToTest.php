<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationAppendToTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::appendTo
     * @covers FluentDOM\Query::apply
     * @covers FluentDOM\Query::appendChildren
     */
    public function testAppendTo() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//span')
        ->appendTo('//div[@id = "foo"]');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

  }
}