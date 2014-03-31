<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationOuterXmlTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::outerXml
     */
    public function testOuterXmlRead() {
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
     * @covers FluentDOM\Query::outerXml
     */
    public function testOuterXmlReadWithTextNodes() {
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
     * @covers FluentDOM\Query::outerXml
     */
    public function testOuterXmlReadEmpty() {
      $xml = $this
        ->getQueryFixtureFromString('<items/>')
        ->find('/items/*')
        ->outerXml();
      $this->assertEquals('', $xml);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::outerXml
     */
    public function testOuterXmlWrite() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p[position() = last()]')
        ->outerXml('<b>New</b>World');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::outerXml
     */
    public function testOuterXmlWriteEmpty() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->outerXml('');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::outerXml
     */
    public function testOuterXmlWriteWithCallback() {
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
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query::outerXml
     */
    public function testOuterXmlWriteWithInvalidDataExpectingException() {
      $fd = new Query();
      $this->setExpectedException('UnexpectedValueException');
      @$fd->outerXml(new \stdClass());
    }
  }
}