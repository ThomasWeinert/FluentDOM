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

  public static function provideDataAttributes() {
    return array(
      'string' => array('World', 'Hello', '<node data-Hello="World"/>'),
      'boolean true' => array(TRUE, 'truth', '<node data-truth="true"/>'),
      'boolean false' => array(FALSE, 'lie', '<node data-lie="false"/>'),
      'array' => array(array('1', '2'), 'list', '<node data-list="[1, 2]"/>'),
      'object' => array(
         self::createObjectFromArray(array('foo' => 'bar')),
         'object',
         '<node data-object=\'{"foo":"bar"}\'/>'
      ),
      'invalid object' => array(NULL, 'object', '<node data-object=\'{{"foo":"bar"}\'/>')
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