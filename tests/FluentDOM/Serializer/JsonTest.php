<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Serializer {

  use FluentDOM\DOM\Document;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class JsonTest extends TestCase {

    /**
     * @covers \FluentDOM\Serializer\Json
     * @dataProvider provideJsonExamples
     * @param string $expected
     * @param string $data
     * @param int $options
     * @param int $depth
     */
    public function testToString(
      $expected, $data, $options = 0, $depth = 256
    ) {
      $serializer = new Json_TestProxy(new \DOMDocument(), $options, $depth);
      $serializer->jsonData = $data;
      $this->assertEquals(
        $expected, (string)$serializer
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json
     */
    public function testToStringWithLimitedDepthExpectingEmptyString(): void {
      $serializer = new Json_TestProxy(new \DOMDocument(), 0, 1);
      $serializer->jsonData = self::getArrayAsStdClass(
        [
          'alice' => 'bob',
          'depth' => self::getArrayAsStdClass(
            [
              'charlie' => 'david'
            ]
          )
        ]
      );
      $this->assertEquals('', (string)$serializer);
    }


    /**
     * @covers \FluentDOM\Serializer\Json
     * @dataProvider provideJsonExamples
     * @param string $expected
     * @param string $data
     * @param int $options
     * @param int $depth
     */
    public function testJsonSerializable(
      $expected, $data, $options = 0, $depth = 256
    ) {
      $serializer = new Json_TestProxy(new \DOMDocument());
      $serializer->jsonData = $data;
      $json = version_compare(PHP_VERSION, '5.5.0', '>=')
        ? json_encode($serializer, $options, $depth)
        : json_encode($serializer, $options);
      $this->assertEquals($expected, $json);
    }

    /**
     * @covers \FluentDOM\Serializer\Json
     */
    public function testJsonSerializableWithLimitedDepthExpectingFalse(): void {
      $serializer = new Json_TestProxy(new \DOMDocument());
      $serializer->jsonData = self::getArrayAsStdClass(
        [
          'alice' => 'bob',
          'depth' => self::getArrayAsStdClass(
            [
              'charlie' => 'david'
            ]
          )
        ]
      );
      $this->assertFalse(
        json_encode($serializer, 0, 1)
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json
     */
    public function testJsonSerializeCallingGetNode(): void {
      $document = new Document();
      $document->appendElement('success');
      $serializer = new Json_TestProxy($document);
      $this->assertEquals(
        '["success"]', json_encode($serializer)
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json
     */
    public function testJsonSerializeCallingGetEmpty(): void {
      $serializer = new Json_TestProxy(new \DOMDocument());
      $this->assertEquals(
        '{}', json_encode($serializer)
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json
     */
    public function testGetNamespaces(): void {
      $document = new Document();
      $document->loadXml(
        '<xml xmlns="urn:1" xmlns:foo="urn:bar">'.
        '<xml xmlns="urn:2" xmlns:foo="urn:foo" xmlns:bar="urn:bar"/>'.
        '</xml>'
      );
      $serializer = new Json_TestProxy($document);
      $this->assertEquals(
        [
          'xmlns:bar' => 'urn:bar',
          'xmlns:foo' => 'urn:foo',
          'xmlns' => 'urn:2'
        ],
        $serializer->getNamespaces($document->documentElement->firstChild)
      );
    }

    public static function getArrayAsStdClass($properties) {
      $data = new \stdClass();
      foreach ($properties as $name => $value) {
        $data->{$name} = $value;
      }
      return $data;
    }
    /**
     * @covers \FluentDOM\Serializer\Json
     * @dataProvider provideExamples
     * @param string $expected
     * @param string $xml
     */
    public function testIntegration($expected, $xml) {
      $document = new \DOMDocument();
      $document->loadXML($xml);
      $serializer = new Json($document);
      $this->assertJsonStringEqualsJsonString(
        $expected,
        (string)$serializer
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json
     */
    public function testIntegrationWithEmptyDocument(): void {
      $serializer = new Json(new \DOMDocument());
      $this->assertEquals(
        '{}', (string)$serializer
      );
    }

    public static function provideJsonExamples() {
      return [
        [
          '{"alice":"bob"}',
          self::getArrayAsStdClass(['alice' => 'bob'])
        ],
        [
          "{\n    \"alice\": \"bob\"\n}",
          self::getArrayAsStdClass(['alice' => 'bob']),
          JSON_PRETTY_PRINT
        ]
      ];
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
        'object, name attribute' => [
          '{"foo":"bar"}',
          '<?xml version="1.0" encoding="UTF-8"?>
           <json:json xmlns:json="urn:carica-json-dom.2013" json:type="object">
             <_ json:name="foo">bar</_>
           </json:json>'
        ],
        'different types' => [
          json_encode(
            [
              'boolean' => TRUE,
              'int' => 42,
              'null' => NULL,
              'string' => 'Foo',
              'array' => [21],
              'object' => new \stdClass()
            ]
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

  class Json_TestProxy extends Json {
    public $jsonData = NULL;

    public function jsonSerialize() {
      return $this->jsonData ?: parent::jsonSerialize();
    }

    public function getNode(\DOMElement $node) {
      return [ $node->nodeName ];
    }

    public function getNamespaces(\DOMElement $node): array {
      return parent::getNamespaces($node);
    }
  }
}
