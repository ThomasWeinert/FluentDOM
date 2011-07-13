<?php
/**
* Collection of tests for the FluentDOMCss class
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
require_once(dirname(__FILE__).'/../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../src/FluentDOM/Data.php');

class FluentDOMDataTest extends FluentDOMTestCase {

  /**
  * @covers FluentDOMData::__construct
  */
  public function testConstructor() {
    $dom = new DOMDocument();
    $dom->appendChild($dom->createElement('sample'));
    $data = new FluentDOMData($dom->documentElement);
    $this->assertAttributeSame(
      $dom->documentElement, '_node', $data
    );
  }

  /**
  * @covers FluentDOMData::toArray
  */
  public function testToArrayWithSeveralAttributes() {
    $dom = new DOMDocument();
    $dom->loadXML(
      '<div data-role="page" data-hidden="true" data-options=\'{"name":"John"}\'></div>'
    );
    $options = new stdClass();
    $options->name = 'John';
    $data = new FluentDOMData($dom->documentElement);
    $this->assertEquals(
      array(
        'role' => 'page',
        'hidden' => TRUE,
        'options' => $options
      ),
      $data->toArray()
    );
  }

  /**
  * @covers FluentDOMData::getIterator
  */
  public function testGetIterator() {
    $dom = new DOMDocument();
    $dom->loadXML(
      '<div data-role="page" data-hidden="true"></div>'
    );
    $data = new FluentDOMData($dom->documentElement);
    $this->assertEquals(
      array(
        'role' => 'page',
        'hidden' => TRUE
      ),
      $data->getIterator()->getArrayCopy()
    );
  }

  /**
  * @covers FluentDOMData::count
  */
  public function testCountExpectingZero() {
    $dom = new DOMDocument();
    $dom->loadXML(
      '<div></div>'
    );
    $data = new FluentDOMData($dom->documentElement);
    $this->assertEquals(
      0, count($data)
    );
  }

  /**
  * @covers FluentDOMData::count
  */
  public function testCountExpectingTwo() {
    $dom = new DOMDocument();
    $dom->loadXML(
      '<div data-role="page" data-hidden="true"></div>'
    );
    $data = new FluentDOMData($dom->documentElement);
    $this->assertEquals(
      2, count($data)
    );
  }

  /**
  * @covers FluentDOMData::__isset
  */
  public function testMagicMethodIssetExpectingTrue() {
    $dom = new DOMDocument();
    $dom->loadXML('<node data-truth="true"/>');
    $data = new FluentDOMData($dom->documentElement);
    $this->assertTrue(isset($data->truth));
  }

  /**
  * @covers FluentDOMData::__isset
  */
  public function testMagicMethodIssetExpectingFalse() {
    $dom = new DOMDocument();
    $dom->loadXML('<node data-truth="true"/>');
    $data = new FluentDOMData($dom->documentElement);
    $this->assertFalse(isset($data->lie));
  }

  /**
  * @covers FluentDOMData::__get
  * @covers FluentDOMData::decodeValue
  * @dataProvider provideDataAttributes
  */
  public function testMagicMethodGet($expected, $name, $xml) {
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $data = new FluentDOMData($dom->documentElement);
    $this->assertEquals(
      $expected,
      $data->$name
    );
  }

  /**
  * @covers FluentDOMData::__set
  * @covers FluentDOMData::encodeValue
  * @dataProvider provideDataValues
  */
  public function testMagicMethodSet($expectedXml, $name, $value) {
    $dom = new DOMDocument();
    $dom->appendChild($dom->createElement('node'));
    $data = new FluentDOMData($dom->documentElement);
    $data->$name = $value;
    $this->assertEquals(
      $expectedXml, $dom->saveXml($dom->documentElement)
    );
  }

  /**
  * @covers FluentDOMData::__unset
  * @dataProvider provideDataValues
  */
  public function testMagicMethodUnset() {
    $dom = new DOMDocument();
    $dom->loadXml('<node data-truth="true"/>');
    $data = new FluentDOMData($dom->documentElement);
    unset($data->truth);
    $this->assertEquals(
      '<node/>', $dom->saveXml($dom->documentElement)
    );
  }

  public static function provideDataAttributes() {
    return array(
      'string' => array('World', 'Hello', '<node data-Hello="World"/>'),
      'boolean true' => array(TRUE, 'truth', '<node data-truth="true"/>'),
      'boolean false' => array(FALSE, 'lie', '<node data-lie="false"/>'),
      'array' => array(
        array('1', '2'),
        'list',
        '<node data-list="[&quot;1&quot;,&quot;2&quot;]"/>'
      ),
      'object' => array(
        self::createObjectFromArray(array('foo' => 'bar')),
        'object',
        '<node data-object="{&quot;foo&quot;:&quot;bar&quot;}"/>'
      ),
      'invalid object' => array(NULL, 'object', '<node data-object=\'{{"foo":"bar"}\'/>'),
      'invalid attrbute' => array(NULL, 'unknown', '<node/>')
    );
  }

  public static function provideDataValues() {
    return array(
      'string' => array('<node data-Hello="World"/>', 'Hello', 'World'),
      'boolean true' => array('<node data-truth="true"/>', 'truth', TRUE),
      'boolean false' => array('<node data-lie="false"/>', 'lie', FALSE),
      'array' => array('<node data-list="[&quot;1&quot;,&quot;2&quot;]"/>', 'list', array('1', '2')),
      'object' => array(
        '<node data-object="{&quot;foo&quot;:&quot;bar&quot;}"/>',
        'object',
        self::createObjectFromArray(array('foo' => 'bar'))
      )
    );
  }

  public static function createObjectFromArray($array) {
    $result = new stdClass();
    foreach ($array as $key => $value) {
      $result->$key = $value;
    }
    return $result;
  }
}