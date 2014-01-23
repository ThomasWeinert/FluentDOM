<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationReplaceAllTest extends TestCase {

    protected $_directory = __DIR__;
    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::replaceAll
     * @covers FluentDOM\Query::apply
     * @covers FluentDOM\Query::insertNodesBefore
     */
    public function testReplaceAll() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->add('<b id="sample">Paragraph. </b>')
        ->replaceAll('//p');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::replaceAll
     * @covers FluentDOM\Query::insertNodesBefore
     */
    public function testReplaceAllWithNode() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->add('<b id="sample">Paragraph. </b>')
        ->replaceAll(
          $fd->find('//p')->item(1)
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::replaceAll
     * @covers FluentDOM\Query::apply
     * @covers FluentDOM\Query::insertNodesBefore
     */
    public function testReplaceAllWithInvalidArgument() {
      $this->setExpectedException('InvalidArgumentException');
      $this->getQueryFixtureFromString(self::XML)
        ->add('<b id="sample">Paragraph. </b>')
        ->replaceAll(
          NULL
        );
    }
  }
}