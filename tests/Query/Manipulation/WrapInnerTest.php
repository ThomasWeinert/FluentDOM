<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationWrapInnerTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationAround
     * @covers FluentDOM\Query::wrapNodes
     * @covers FluentDOM\Query::wrapInner
     */
    public function testWrapInner() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->wrapInner('<b></b>');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationAround
     * @covers FluentDOM\Query::wrapNodes
     * @covers FluentDOM\Query::wrapInner
     */
    public function testWrapInnerWithCallback() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->wrapInner(
          function($node, $index) {
            return '<b class="'.$node->textContent.'_'.$index.'" />';
          }
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}