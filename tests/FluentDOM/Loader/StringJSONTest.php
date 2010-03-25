<?php
/**
* JSON string loader test for FluentDOM
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
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/../../../FluentDOM/Loader/StringJSON.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMLoaderStringJSON.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderStringJSONTest extends PHPUnit_Framework_TestCase {

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::load
  */
  public function testLoad() {
    $loader = new FluentDOMLoaderStringJSON();
    $result = $loader->load(
      '{}',
      'application/json'
    );
    $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $result);
    $this->assertTrue($result[0] instanceof DOMDocument);
    $this->assertSame('json', $result[1][0]->tagName);
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::load
  */
  public function testLoadWithInvalidJSONExpectingException() {
    $loader = new FluentDOMLoaderStringJSON();
    try {
      $result = $loader->load(
        '{foo}',
        'application/json'
      );
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::load
  */
  public function testLoadWithUnknownSourceExpectingFalse() {
    $loader = new FluentDOMLoaderStringJSON();
    $this->assertFalse($loader->load('', 'text/xml'));
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithSimpleObject() {
    $loader = new FluentDOMLoaderStringJSON();
    $result = $loader->load(
      '{"foo":"bar"}',
      'application/json'
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><foo>bar</foo></json>',
      $result[0]
    );
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithBooleans() {
    $loader = new FluentDOMLoaderStringJSON();
    $result = $loader->load(
      '{"a":true,"b":false}',
      'application/json'
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><a>1</a><b>0</b></json>',
      $result[0]
    );
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithChildObjects() {
    $loader = new FluentDOMLoaderStringJSON();
    $result = $loader->load(
      '{"a":{"object":1},"b":{"object":2}}',
      'application/json'
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><a><object>1</object></a><b><object>2</object></b></json>',
      $result[0]
    );
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithArray() {
    $loader = new FluentDOMLoaderStringJSON();
    $result = $loader->load(
      '{"a":[1,2,3]}',
      'application/json'
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><a><a-child>1</a-child><a-child>2</a-child><a-child>3</a-child></a></json>',
      $result[0]
    );
  }

  public function assertDOMDocumentEqualsXmlString($expectedXml, $actualDocument) {
    $actualDocument->formatOutput = FALSE;
    $actualDocument->preserveWhiteSpace = FALSE;
    $this->assertEquals(
      $expectedXml,
      $actualDocument->saveXML($actualDocument->documentElement, LIBXML_NOEMPTYTAG)
    );
  }
}
?>