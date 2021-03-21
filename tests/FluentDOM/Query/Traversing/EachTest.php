<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class TraversingEachTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @covers \FluentDOM\Query::each
     */
    public function testEach(): void {
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
