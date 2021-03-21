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
  use FluentDOM\Exceptions;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class ManipulationAppendTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppend(): void {
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
    public function testAppendXmlString(): void {
      $fd = new Query();
      $fd->append('<strong>Hello</strong>');
      $this->assertEquals('strong', $fd->find('/strong')->item(0)->nodeName);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendDomElement(): void {
      $fd = new Query();
      $fd->append($fd->document->createElement('strong'));
      $this->assertEquals('strong', $fd->find('/strong')->item(0)->nodeName);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendDomnodelist(): void {
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
    public function testAppendWithCallback(): void {
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
    public function testAppendOnEmptyDocumentWithCallback(): void {
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
    public function testAppendNodeWithCallback(): void {
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
    public function testAppendFragmentWithMultipleNodesToDocument(): void {
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
    public function testAppendWithMultipleNodesFromOtherDomToDocument(): void {
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
    public function testAppendWithElementFromOtherDocument(): void {
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
    public function testAppendWithTextNodeFromOtherDocument(): void {
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
    public function testAppendWithInvalidArgumentExpectingException(): void {
      $fd = new Query();
      $this->expectException(Exceptions\LoadingError::class);
      $fd->append(new \stdClass());
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testAppendWithEmptyArgumentExpectingException(): void {
      $fd = new Query();
      $this->expectException(Exceptions\LoadingError::class);
      $fd->append([]);
    }
  }
}
