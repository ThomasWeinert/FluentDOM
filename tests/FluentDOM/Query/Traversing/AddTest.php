<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingAddTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::add
     * @covers FluentDOM\Query::uniqueSortNodes
     */
    public function testAddElements() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->add(
          $fd->find('//div')
        )
        ->toggleClass('inB');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::add
     * @covers FluentDOM\Query::uniqueSortNodes
     */
    public function testAddFromExpression() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->add('//div')
        ->toggleClass('inB');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::add
     * @covers FluentDOM\Query::uniqueSortNodes
     */
    public function testAddInContext() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->add('.//b')
        ->toggleClass('inB');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::add
     */
    public function testAddWithContext() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->add('.//b', $fd->document->documentElement)
        ->toggleClass('inB');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}