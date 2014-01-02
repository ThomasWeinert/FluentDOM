<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingNextTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::next
     */
    public function testNext() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd ->find('//button[@disabled]')
        ->next()
        ->text('This button is disabled.');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}