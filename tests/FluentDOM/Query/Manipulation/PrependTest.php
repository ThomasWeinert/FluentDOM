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

  class ManipulationPrependTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testPrepend(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->prepend('<strong>Hello</strong>');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testPrependWithCallback(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->prepend(
          function($node, $index) {
            return 'Hello #'.($index + 1);
          }
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testPrependOnEmptyElement(): void {
      $fd = new Query();
      $fd->document->appendElement('test');
      $fd->find('/test')->prepend('success');
      $this->assertXmlStringEqualsXmlString(
        '<test>success</test>',
        (string)$fd
      );
    }
  }
}
