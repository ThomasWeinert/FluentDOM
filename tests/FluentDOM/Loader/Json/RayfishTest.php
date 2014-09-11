<?php
namespace FluentDOM\Loader\Json {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class RayfishTest extends TestCase {


    /**
     * @covers FluentDOM\Loader\Json\Rayfish
     * @dataProvider provideExamples
     * @param string $json
     * @param string $xml
     */
    public function testIntegeration($json, $xml) {
      $loader = new Rayfish();
      $dom = $loader->load($json, 'Rayfish');
      $this->assertXmlStringEqualsXmlString(
        $xml, $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Loader\Json\Rayfish
     */
    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new Rayfish();
      $this->assertNull(
        $loader->load(
          NULL,
          'rayfish'
        )
      );
    }

    public  static function provideExamples() {
      return [
        'Simple element' => [
          '{ "#name": "alice", "#text": null, "#children": [ ] }',
          '<alice/>'
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