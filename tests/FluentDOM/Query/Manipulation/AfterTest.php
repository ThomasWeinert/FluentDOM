<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationAfterTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationOutside
     * @covers \FluentDOM\Query
     */
    public function testAfter() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd ->formatOutput()
        ->find('//p')
        ->after('<b>Hello</b>')
        ->after(' World');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationOutside
     * @covers \FluentDOM\Query
     */
    public function testAfterWithFunction() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd ->formatOutput()
        ->find('//p')
        ->after(
          function($node, $index, $content) {
            return '<p index="'.$index.'">Hi</p>';
          }
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}