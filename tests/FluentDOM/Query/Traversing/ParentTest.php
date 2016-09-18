<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingParentTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::parent
     */
    public function testParent() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//body//*')
        ->each(
          function($node) {
            $fd = new Query();
            $fd->load($node);
            $fd->prepend(
              $fd->document->createTextNode(
                $fd->parent()->item(0)->tagName." > "
              )
             );
          }
        );
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}