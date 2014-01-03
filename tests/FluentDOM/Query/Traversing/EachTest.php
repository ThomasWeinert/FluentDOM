<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingEachTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @covers FluentDOM\Query::each
     */
    public function testEach() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//body//*')
        ->each(
            function (\DOMElement $node) {
              $node->insertBefore(
                $node->ownerDocument->createTextNode('EACH > '),
                $node->firstChild
              );
            }
          );
      $this->assertTrue($fd instanceof Query);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }
  }
}