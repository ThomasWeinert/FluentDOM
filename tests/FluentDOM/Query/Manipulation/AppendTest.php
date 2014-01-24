<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationAppendTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::append
     * @covers FluentDOM\Query::getContentElement
     * @covers FluentDOM\Query::getContentNodes
     */
    public function testAppend() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->append('<strong>Hello</strong>');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::append
     * @covers FluentDOM\Query::getContentElement
     * @covers FluentDOM\Query::getContentNodes
     */
    public function testAppendXmlString() {
      $fd = new Query();
      $fd->append('<strong>Hello</strong>');
      $this->assertEquals('strong', $fd->find('/strong')->item(0)->nodeName);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::append
     * @covers FluentDOM\Query::getContentElement
     * @covers FluentDOM\Query::getContentNodes
     */
    public function testAppendDomElement() {
      $fd = new Query();
      $fd->append($fd->document->createElement('strong'));
      $this->assertEquals('strong', $fd->find('/strong')->item(0)->nodeName);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::append
     * @covers FluentDOM\Query::getContentElement
     * @covers FluentDOM\Query::getContentNodes
     */
    public function testAppendDomnodelist() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $items = $fd->find('//item');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $doc = $fd
        ->find('//html/div')
        ->append($items);
      $this->assertInstanceOf('FluentDOM\\Query', $doc);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::append
     * @covers FluentDOM\Query::getContentElement
     * @covers FluentDOM\Query::getContentNodes
     */
    public function testAppendWithCallback() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $doc = $fd
        ->find('//p')
        ->append(
          function ($node, $index, $content) {
            return strrev($content);
          }
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::append
     * @covers FluentDOM\Query::getContentElement
     * @covers FluentDOM\Query::getContentNodes
     */
    public function testAppendOnEmptyDocumentWithCallback() {
      $fd = new Query();
      $doc = $fd->append(
        function () {
          return '<sample>Hello World</sample>';
        }
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'."\n".'<sample>Hello World</sample>',
        $doc->document->saveXML()
      );
    }
  }
}