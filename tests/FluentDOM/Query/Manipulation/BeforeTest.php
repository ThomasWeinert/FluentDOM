<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationBeforeTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationOutside
     * @covers FluentDOM\Query::before
     */
    public function testBefore() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->formatOutput()
        ->find('//p')
        ->before(' World')
        ->before('<b>Hello</b>');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationOutside
     * @covers FluentDOM\Query::before
     */
    public function testBeforeWithFunction() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->formatOutput()
        ->find('//p')
        ->before(
          function($node, $index) {
            return '<p index="'.$index.'">Hi</p>';
          }
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}