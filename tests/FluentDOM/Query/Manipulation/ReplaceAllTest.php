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

  class ReplaceAllTest extends TestCase {

    protected $_directory = __DIR__;
    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testReplaceAll(): void {
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
    public function testReplaceAllWithNode(): void {
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
    public function testReplaceAllWithInvalidArgument(): void {
      $this->expectException(\InvalidArgumentException::class);
      $this->getQueryFixtureFromString(self::XML)
        ->add('<b id="sample">Paragraph. </b>')
        ->replaceAll(
          NULL
        );
    }
  }
}
