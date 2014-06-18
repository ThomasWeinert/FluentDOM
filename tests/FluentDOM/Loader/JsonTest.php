<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class JsonTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testSupportsExpectingTrue() {
      $loader = new Json();
      $this->assertTrue($loader->supports('json'));
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testSupportsExpectingFalse() {
      $loader = new Json();
      $this->assertFalse($loader->supports('text/xml'));
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadWithValidJson() {
      $loader = new Json();
      $this->assertInstanceOf(
        'DOMDocument',
        $dom = $loader->load(
          '{"foo":"bar"}',
          'json'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json xmlns:json="urn:carica-json-dom.2013">'.
        '<foo>bar</foo>'.
        '</json:json>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadWithValidFile() {
      $loader = new Json();
      $this->assertInstanceOf(
        'DOMDocument',
        $dom = $loader->load(
          __DIR__.'/TestData/loader.json',
          'json'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json xmlns:json="urn:carica-json-dom.2013">'.
        '<foo>bar</foo>'.
        '</json:json>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadWithValidStructure() {
      $loader = new Json();
      $json = new \stdClass();
      $json->foo = 'bar';
      $this->assertInstanceOf(
        'DOMDocument',
        $dom = $loader->load(
          $json, 'json'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json xmlns:json="urn:carica-json-dom.2013">'.
        '<foo>bar</foo>'.
        '</json:json>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadWithValidJsonVerbose() {
      $loader = new Json(Json::OPTION_VERBOSE);
      $this->assertInstanceOf(
        'DOMDocument',
        $dom = $loader->load(
          '{"foo":"bar"}',
          'json'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json'.
        ' xmlns:json="urn:carica-json-dom.2013"'.
        ' json:type="object">'.
        '<foo json:name="foo" json:type="string">bar</foo>'.
        '</json:json>',
        $dom->saveXml()
      );
    }
    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadWithDifferentDataTypes() {
      $loader = new Json();
      $dom = $loader->load(
        json_encode(
          array(
            'boolean' => TRUE,
            'int' => 42,
            'null' => NULL,
            'string' => 'Foo',
            'array' => array(21),
            'object' => new \stdClass()
          )
        ),
        'json'
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>
         <json:json xmlns:json="urn:carica-json-dom.2013">
           <boolean json:type="boolean">true</boolean>
           <int json:type="number">42</int>
           <null json:type="null"/>
           <string>Foo</string>
           <array json:type="array">
             <_ json:type="number">21</_>
           </array>
           <object json:type="object"/>
         </json:json>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new Json();
      $this->assertNull(
        $loader->load(
          NULL,
          'json'
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadWithInvalidJsonStringExpectingException() {
      $loader = new Json();
      $this->setExpectedException('UnexpectedValueException');
      $loader->load(
        '{foo}}',
        'json'
      );
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadStoppingAtMaxDepth() {
      $loader = new Json(0, 1);
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>
         <json:json xmlns:json="urn:carica-json-dom.2013"><foo/></json:json>',
        $loader
          ->load(json_encode(['foo' => [1, 2, 3]]), 'json')
          ->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Loader\Json
     */
    public function testLoadWithEmptyArray() {
      $loader = new Json(0, 1);
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>
         <json:json xmlns:json="urn:carica-json-dom.2013" json:type="array"/>',
        $loader
          ->load('[]', 'json')
          ->saveXML()
      );
    }
  }
}