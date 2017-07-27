<?php
namespace FluentDOM\Serializer\Json {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class RayfishTest extends TestCase {

    /**
     * @covers \FluentDOM\Serializer\Json\Rayfish
     * @dataProvider provideExamples
     * @param string $expected
     * @param string $xml
     */
    public function testIntegration($expected, $xml) {
      $document = new \DOMDocument();
      $document->loadXML($xml);
      $serializer = new Rayfish($document);
      $this->assertJsonStringEqualsJsonString(
        $expected,
        json_encode($serializer)
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json\Rayfish
     */
    public function testIntegrationWithEmptyDocument() {
      $serializer = new Rayfish(new \DOMDocument());
      $this->assertEquals(
        '{}', (string)$serializer
      );
    }

    public  static function provideExamples() {
      return [
        'Simple element' => [
          '{ "#name": "alice", "#text": null, "#children": [ ] }',
          '<alice/>'
        ],
        'Simple element with text' => [
          '{ "#name": "alice", "#text": "bob", "#children": [ ] }',
          '<alice>bob</alice>'
        ],
        'Simple element with cdata' => [
          '{ "#name": "alice", "#text": " bob ", "#children": [ ] }',
          '<alice><![CDATA[ bob ]]></alice>'
        ],
        'Nested element' => [
          '{ "#name": "alice", "#text": null, "#children": [
            { "#name": "bob", "#text": "charlie", "#children": [ ] },
            { "#name": "david", "#text": "edgar", "#children": [ ] }
          ]}',
          '<alice><bob>charlie</bob><david>edgar</david></alice>'
        ],
        'Attributes' => [
          '{ "#name": "alice", "#text": "bob", "#children": [
            { "#name": "@charlie",
              "#text": "david",
              "#children": [ ]
            }
          ]}',
          '<alice charlie="david">bob</alice>'
        ],
        'Default Namespace' => [
          '{ "#name": "alice", "#text": null, "#children": [
            { "#name": "@xmlns", "#text": "urn:bob", "#children": [ ]}
           ]}',
          '<alice xmlns="urn:bob"/>'
        ],
        'Namespace' => [
          '{ "#name": "charlie:alice", "#text": null, "#children": [
            { "#name": "@xmlns:charlie", "#text": "urn:bob", "#children": [ ]}
           ]}',
          '<charlie:alice xmlns:charlie="urn:bob"/>'
        ],
      ];
    }
  }
}