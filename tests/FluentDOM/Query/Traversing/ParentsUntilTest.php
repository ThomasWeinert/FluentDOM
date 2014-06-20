<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingParentsUntilTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::parentsUntil
     */
    public function testParentsUntil() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//li[contains(concat(" ", normalize-space(@class), " "),  " item-a ")]')
        ->parentsUntil('contains(concat(" ", normalize-space(@class), " "),  " level-1 ")')
        ->addClass('selectedParent');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}