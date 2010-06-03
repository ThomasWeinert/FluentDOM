<?php
/**
* Collection of test for the FluentDOMHandler class supporting PHP 5.2
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2010 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* load necessary files
*/
require_once (dirname(__FILE__).'/../FluentDOMTestCase.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMHandler.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMHandlerTest extends FluentDOMTestCase {

  /**
  * @group Handler
  * @covers FluentDOMHandler::insertNodesAfter
  */
  public function testInsertNodesAfter() {
    $dom = $this->fixtureGetSampleDom();
    $nodes = array(
      $dom->createElement('child'),
      $dom->createTextNode('Hello World')
    );
    $resultNodes = FluentDOMHandler::insertNodesAfter(
      $dom->documentElement->childNodes->item(0), $nodes
    );
    $this->assertSame(
      '<sample><samplechild/><child/>Hello World</sample>',
      $dom->saveXML($dom->documentElement)
    );
    $this->assertEquals($nodes, $resultNodes);
    $this->assertNotSame($nodes[0], $resultNodes[0]);
    $this->assertNotSame($nodes[1], $resultNodes[1]);
  }

  /**
  * @group Handler
  * @covers FluentDOMHandler::insertNodesBefore
  */
  public function testInsertNodesBefore() {
    $dom = $this->fixtureGetSampleDom();
    $nodes = array(
      $dom->createElement('child'),
      $dom->createTextNode('Hello World')
    );
    $resultNodes = FluentDOMHandler::insertNodesBefore(
      $dom->documentElement->childNodes->item(0), $nodes
    );
    $this->assertSame(
      '<sample><child/>Hello World<samplechild/></sample>',
      $dom->saveXML($dom->documentElement)
    );
    $this->assertEquals($nodes, $resultNodes);
    $this->assertNotSame($nodes[0], $resultNodes[0]);
    $this->assertNotSame($nodes[1], $resultNodes[1]);
  }

  /**
  * @group Handler
  * @covers FluentDOMHandler::appendChildren
  */
  public function testAppendChildren() {
    $dom = $this->fixtureGetSampleDom();
    $nodes = array(
      $dom->createElement('child'),
      $dom->createTextNode('Hello World')
    );
    $resultNodes = FluentDOMHandler::appendChildren(
      $dom->documentElement, $nodes
    );
    $this->assertSame(
      '<sample><samplechild/><child/>Hello World</sample>',
      $dom->saveXML($dom->documentElement)
    );
    $this->assertEquals($nodes, $resultNodes);
    $this->assertNotSame($nodes[0], $resultNodes[0]);
    $this->assertNotSame($nodes[1], $resultNodes[1]);
  }

  /**
  * @group Handler
  * @covers FluentDOMHandler::insertChildrenBefore
  */
  public function testInsertChildrenBefore() {
    $dom = $this->fixtureGetSampleDom();
    $nodes = array(
      $dom->createElement('child'),
      $dom->createTextNode('Hello World')
    );
    $resultNodes = FluentDOMHandler::insertChildrenBefore(
      $dom->documentElement, $nodes
    );
    $this->assertSame(
      '<sample><child/>Hello World<samplechild/></sample>',
      $dom->saveXML($dom->documentElement)
    );
    $this->assertEquals($nodes, $resultNodes);
    $this->assertNotSame($nodes[0], $resultNodes[0]);
    $this->assertNotSame($nodes[1], $resultNodes[1]);
  }

  public function fixtureGetSampleDom() {
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = FALSE;
    $dom->formatOutput = FALSE;
    $dom->appendChild($dom->createElement('sample'));
    $dom->documentElement->appendChild($dom->createElement('samplechild'));
    return $dom;
  }
}
?>