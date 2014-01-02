<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationPrependTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::prepend
     */
    public function testPrepend() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->prepend('<strong>Hello</strong>');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::prepend
     */
    public function testPrependWithCallback() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->prepend(
          function($node, $index) {
            return 'Hello #'.($index + 1);
          }
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}