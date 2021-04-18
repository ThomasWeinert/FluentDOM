<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query\Traversing {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class ChildrenTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::children
     */
    public function testChildren(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__)
        ->find('//div[@id = "container"]/p')
        ->children();
      $this->assertEquals(2, $fd->length);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::children
     */
    public function testChildrenExpression(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__)
        ->find('//div[@id = "container"]/p')
        ->children('name() = "em"');
      $this->assertEquals(1, $fd->length);
    }
  }
}
