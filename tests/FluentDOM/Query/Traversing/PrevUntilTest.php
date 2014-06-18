<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingPrevUntilTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::prevUntil
     * @covers FluentDOM\Query::expandUntil
     */
    public function testPrevUntil() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//*[@id = "term-2"]')
        ->prevUntil('name() = "dt"')
        ->addClass('previous');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}