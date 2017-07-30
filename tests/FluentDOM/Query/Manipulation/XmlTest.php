<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class ManipulationXmlTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testXmlRead() {
      $expect = '<item index="0">text1</item>'.
        '<item index="1">text2</item>'.
        '<item index="2">text3</item>';
      $xml = $this->getQueryFixtureFromString(self::XML)->find('//group')->xml();
      $this->assertEquals($expect, $xml);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testXmlReadWithTextNodes() {
      $expect = 'text1';
      $xml = $this->getQueryFixtureFromString(self::XML)
        ->find('//group/item/text()')
        ->xml();
      $this->assertEquals($expect, $xml);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testXmlReadEmpty() {
      $xml = $this->getQueryFixtureFromString('<items/>')->find('/items/*')->xml();
      $this->assertEquals('', $xml);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testXmlWrite() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p[position() = last()]')
        ->xml('<b>New</b>World');
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testXmlWriteEmpty() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->xml('');
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testXmlWriteWithCallback() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->xml(
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
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testXmlWriteWithInvalidDataExpectingException() {
      $fd = new Query();
      $this->expectException(\UnexpectedValueException::class);
      @$fd->xml(new \stdClass());
    }
  }
}