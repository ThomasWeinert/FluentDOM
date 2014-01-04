<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingHasTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers FluentDOM\Query::has
     */
    public function testHas() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//li')
        ->has('name() = "ul"')
        ->addClass('withSubList');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFilter
     * @covers FluentDOM\Query::has
     */
    public function testHasWithNode() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $node = $fd->find('//ul')->item(1);
      $fd
        ->find('//li')
        ->has($node)
        ->addClass('withSubList');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}