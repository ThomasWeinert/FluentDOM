<?php
/**
* Collection of tests for the FluentDOMAttributes class
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage unitTests
*/

/**
* load necessary files
*/
require_once (dirname(__FILE__).'/../FluentDOMTestCase.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class FluentDOMAttributesTest extends FluentDOMTestCase {

  /**
  * @covers FluentDOMAttributes::__construct
  */
  public function testConstructor() {
    $fd = $this->getMock('FluentDOM');
    $attr = new FluentDOMAttributes($fd);
    $this->assertAttributeSame(
      $fd, '_fd', $attr
    );
  }

  /**
  * @covers FluentDOMAttributes::toArray
  */
  public function testToArray() {
    $fd = $this->getFluentDOMWithNodeFixture(
      $this->getSimpleDocumentNodeFixture()
    );
    $attr = new FluentDOMAttributes($fd);
    $this->assertEquals(
      array('foo' => 1, 'bar' => 2),
      $attr->toArray()
    );
  }

  /**
  * @covers FluentDOMAttributes::count
  */
  public function testCountExpectingTwo() {
    $fd = $this->getFluentDOMWithNodeFixture(
      $this->getSimpleDocumentNodeFixture()
    );
    $attr = new FluentDOMAttributes($fd);
    $this->assertEquals(
      2, count($attr)
    );
  }

  /**
  * @covers FluentDOMAttributes::getIterator
  */
  public function testGetIterator() {
    $fd = $this->getFluentDOMWithNodeFixture(
      $this->getSimpleDocumentNodeFixture()
    );
    $attr = new FluentDOMAttributes($fd);
    $iterator = $attr->getIterator();
    $this->assertEquals(
      array('foo' => 1, 'bar' => 2),
      $iterator->getArrayCopy()
    );
  }

  /********************
  * Fixtures
  ********************/

  public function getFluentDOMWithNodeFixture($node) {
    $fd = $this->getMock('FluentDOM');
    $fd
      ->expects($this->any())
      ->method('offsetExists')
      ->with(0)
      ->will($this->returnValue(TRUE));
    $fd
      ->expects($this->any())
      ->method('offsetGet')
      ->with(0)
      ->will($this->returnValue($node));
    return $fd;
  }

  public function getSimpleDocumentNodeFixture() {
    $dom = new DOMDocument;
    $node = $dom->createElement('sample');
    $node->setAttribute('foo', 1);
    $node->setAttribute('bar', 2);
    $dom->appendChild($node);
    return $node;
  }
}