<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\Exceptions;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationAppendTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppend() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->append('<strong>Hello</strong>');
      $this->assertInstanceOf(Query::class, $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendXmlString() {
      $fd = new Query();
      $fd->append('<strong>Hello</strong>');
      $this->assertEquals('strong', $fd->find('/strong')->item(0)->nodeName);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendDomElement() {
      $fd = new Query();
      $fd->append($fd->document->createElement('strong'));
      $this->assertEquals('strong', $fd->find('/strong')->item(0)->nodeName);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendDomnodelist() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $items = $fd->find('//item');
      $this->assertInstanceOf(Query::class, $fd);
      $doc = $fd
        ->find('//html/div')
        ->append($items);
      $this->assertInstanceOf(Query::class, $doc);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendWithCallback() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
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
     * @covers \FluentDOM\Query
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

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendNodeWithCallback() {
      $fd = (new Query('<sample/>'))
        ->find('/*')
        ->append(
          function () {
            return 'Hello World';
          }
        );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'."\n".'<sample>Hello World</sample>',
        (string)$fd
      );
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendFragmentWithMultipleNodesToDocument() {
      $fd = new Query();
      $fd->append('<first/><second/>');
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'."\n".'<first/>',
        (string)$fd
      );
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendWithMultipleNodesFromOtherDomToDocument() {
      $document = new \DOMDocument();
      $fd = new Query();
      $fd->append(
        [
          $document->createElement('first'),
          $document->createElement('second')
        ]
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'."\n".'<first/>',
        (string)$fd
      );
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendWithElementFromOtherDocument() {
      $document = new \DOMDocument();
      $fd = new Query();
      $fd->append($document->createElement('first'));
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'."\n".'<first/>',
        (string)$fd
      );
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendWithTextNodeFromOtherDocument() {
      $document = new \DOMDocument();
      $fd = new Query();
      $fd
        ->append('<first/>')
        ->append($document->createTextNode('text'));
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'."\n".'<first>text</first>',
        (string)$fd
      );
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendWithInvalidArgumentExpectingException() {
      $fd = new Query();
      $this->expectException(Exceptions\LoadingError::class);
      $fd->append(new \stdClass());
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendWithEmptyArgumentExpectingException() {
      $fd = new Query();
      $this->expectException(Exceptions\LoadingError::class);
      $fd->append([]);
    }
  }
}