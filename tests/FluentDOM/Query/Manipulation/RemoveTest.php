<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationRemoveTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationRemove
     * @covers FluentDOM\Query::remove
     */
    public function testRemove() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p[@class = "first"]')
        ->remove();
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationRemove
     * @covers FluentDOM\Query::remove
     */
    public function testRemoveWithExpression() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->remove('@class = "first"');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationRemove
     * @covers FluentDOM\Query::remove
     */
    public function testAppendRemovedNodes() {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->remove()
        ->appendTo('//group');
      $this->assertXmlStringEqualsXmlString(self::XML, (string)$fd);
    }
  }
}