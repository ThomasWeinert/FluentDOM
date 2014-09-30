<?php
namespace FluentDOM\Serializer\Json {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class JsonDOMTest extends TestCase {

    /**
     * @covers FluentDOM\Serializer\Json\JsonDOM
     * @dataProvider provideExamples
     * @param string $expected
     * @param string $xml
     */
    public function testIntegration($expected, $xml) {
      $dom = new \DOMDocument();
      $dom->loadXML($xml);
      $serializer = new JsonDOM($dom);
      $this->assertJsonStringEqualsJsonString(
        $expected,
        (string)$serializer
      );
    }

    /**
     * @covers FluentDOM\Serializer\Json\JsonDOM
     */
    public function testIntegrationWithEmptyDocument() {
      $serializer = new JsonDOM(new \DOMDocument());
      $this->assertEquals(
        '{}', (string)$serializer
      );
    }

    public  static function provideExamples() {
      return [
        'object' => [
          '{"foo":"bar"}',
          '<?xml version="1.0" encoding="UTF-8"?>
          <json:json xmlns:json="urn:carica-json-dom.2013">
            <foo>bar</foo>
          </json:json>'
        ],
        'object, all attributes' => [
          '{"foo":"bar"}',
          '<?xml version="1.0" encoding="UTF-8"?>
           <json:json xmlns:json="urn:carica-json-dom.2013" json:type="object">
             <foo json:name="foo" json:type="string">bar</foo>
           </json:json>'
        ],
        'different types' => [
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
           </json:json>'
        ]
      ];
    }
  }
}