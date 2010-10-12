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
require_once(dirname(__FILE__).'/../../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../../src/FluentDOM/Loader/StringJSON.php');

/**
* Test class for FluentDOMLoaderStringJSON.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMLoaderStringJSONTest extends FluentDOMTestCase {

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::load
  */
  public function testLoad() {
    $loader = new FluentDOMLoaderStringJSON();
    $contentType = 'application/json';
    $result = $loader->load(
      '{}',
      $contentType
    );
    $this->assertTrue($result instanceof DOMNode);
    $this->assertEquals('json', $result->tagName);
    $this->assertEquals('text/xml', $contentType);
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::load
  */
  public function testLoadWithInvalidJsonExpectingException() {
    $loader = new FluentDOMLoaderStringJSON();
    try {
      $contentType = 'application/json';
      $result = $loader->load(
        '{foo}',
        $contentType
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
    $contentType = 'text/xml';
    $this->assertNull($loader->load('', $contentType));
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_addElement
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithSimpleObject() {
    $loader = new FluentDOMLoaderStringJSON();
    $contentType = 'application/json';
    $result = $loader->load(
      '{"foo":"bar"}',
      $contentType
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><foo>bar</foo></json>',
      $result->ownerDocument
    );
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_addElement
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithSimpleObjectAndTypeAttributes() {
    $loader = new FluentDOMLoaderStringJSON();
    $loader->typeAttributes = TRUE;
    $contentType = 'application/json';
    $result = $loader->load(
      '{"foo":"bar"}',
      $contentType
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json type="object"><foo type="string">bar</foo></json>',
      $result->ownerDocument
    );
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_addElement
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithComplexPropertyName() {
    $loader = new FluentDOMLoaderStringJSON();
    $contentType = 'application/json';
    $result = $loader->load(
      '{"foo bar":"bar"}',
      $contentType
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><foo-bar name="foo bar">bar</foo-bar></json>',
      $result->ownerDocument
    );
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_addElement
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithBooleans() {
    $loader = new FluentDOMLoaderStringJSON();
    $contentType = 'application/json';
    $result = $loader->load(
      '{"a":true,"b":false}',
      $contentType
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><a>1</a><b>0</b></json>',
      $result->ownerDocument
    );
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_addElement
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithChildObjects() {
    $loader = new FluentDOMLoaderStringJSON();
    $contentType = 'application/json';
    $result = $loader->load(
      '{"a":{"object":1},"b":{"object":2}}',
      $contentType
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><a><object>1</object></a><b><object>2</object></b></json>',
      $result->ownerDocument
    );
  }

  /**
  * @group Loaders
  * @group LoadersJSON
  * @covers FluentDOMLoaderStringJSON::_addElement
  * @covers FluentDOMLoaderStringJSON::_toDom
  */
  public function testLoadWithArray() {
    $loader = new FluentDOMLoaderStringJSON();
    $contentType = 'application/json';
    $result = $loader->load(
      '{"a":[1,2,3]}',
      $contentType
    );
    $this->assertDOMDocumentEqualsXmlString(
      '<json><a><a-child>1</a-child><a-child>2</a-child><a-child>3</a-child></a></json>',
      $result->ownerDocument
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
