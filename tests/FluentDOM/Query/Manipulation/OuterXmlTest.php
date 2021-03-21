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

  class ManipulationOuterXmlTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterXmlRead(): void {
      $expect = '<group id="1st">'.
        '<item index="0">text1</item>'.
        '<item index="1">text2</item>'.
        '<item index="2">text3</item>'.
        '</group>';
      $xml = $this
        ->getQueryFixtureFromString(self::XML)
        ->find('//group')
        ->outerXml();
      $this->assertXmlStringEqualsXmlString($expect, $xml);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterXmlReadWithTextNodes(): void {
      $expect = 'text1';
      $xml = $this
        ->getQueryFixtureFromString(self::XML)
        ->find('//group/item/text()')
        ->outerXml();
      $this->assertEquals($expect, $xml);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterXmlReadEmpty(): void {
      $xml = $this
        ->getQueryFixtureFromString('<items/>')
        ->find('/items/*')
        ->outerXml();
      $this->assertEquals('', $xml);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterXmlWrite(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p[position() = last()]')
        ->outerXml('<b>New</b>World');
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterXmlWriteEmpty(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->outerXml('');
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterXmlWriteWithCallback(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->outerXml(
          function($node, $index, $xml) {
            if ($index == 1) {
              return '';
            } else {
              return strtoupper($xml);
            }
          }
        );
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterXmlWriteWithInvalidDataExpectingException(): void {
      $fd = new Query();
      $this->expectException(\UnexpectedValueException::class);
      @$fd->outerXml(new \stdClass());
    }
  }
}
