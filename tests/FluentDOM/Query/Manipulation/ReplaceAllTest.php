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
     * @covers \FluentDOM\Query
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
     * @covers \FluentDOM\Query
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
     * @covers \FluentDOM\Query
     */
    public function testReplaceAllWithInvalidArgument() {
      $this->expectException(\InvalidArgumentException::class);
      $this->getQueryFixtureFromString(self::XML)
        ->add('<b id="sample">Paragraph. </b>')
        ->replaceAll(
          NULL
        );
    }
  }
}