<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query\Manipulation {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class RemoveTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationRemove
     * @covers \FluentDOM\Query
     */
    public function testRemove(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p[@class = "first"]')
        ->remove();
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationRemove
     * @covers \FluentDOM\Query
     */
    public function testRemoveWithExpression(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->remove('@class = "first"');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationRemove
     * @covers \FluentDOM\Query
     */
    public function testAppendRemovedNodes(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->remove()
        ->appendTo('//group');
      $this->assertXmlStringEqualsXmlString(self::XML, (string)$fd);
    }
  }
}
