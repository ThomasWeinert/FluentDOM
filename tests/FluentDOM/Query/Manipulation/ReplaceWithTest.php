<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationReplaceWithTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::replaceWith
     * @covers FluentDOM\Query::apply
     * @covers FluentDOM\Query::getNodes
     * @covers FluentDOM\Query::insertNodesBefore
     */
    public function testReplaceWith() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->replaceWith('<b>Paragraph. </b>');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::replaceWith
     * @covers FluentDOM\Query::apply
     * @covers FluentDOM\Query::getNodes
     * @covers FluentDOM\Query::insertNodesBefore
     */
    public function testReplaceWithWithFunction() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->replaceWith(
          function ($node, $index) {
            return '<div index="'.$index.'">'.$node->textContent.'</div>';
          }
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}