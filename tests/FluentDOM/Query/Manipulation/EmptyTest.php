<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationEmptyTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationRemove
     * @covers FluentDOM::__call
     * @covers FluentDOM::_emptyNodes
     */
    public function testEmpty() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p[@class = "first"]')
        ->empty();
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

  }
}