<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Serializer\Json {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class JsonMLTest extends TestCase {

    /**
     * @covers \FluentDOM\Serializer\Json\JsonML
     * @dataProvider provideExamples
     * @param string $expected
     * @param string $xml
     */
    public function testIntegration(string $expected, string $xml): void {
      $document = new \DOMDocument();
      $document->loadXML($xml);
      $serializer = new JsonML($document);
      $this->assertJsonStringEqualsJsonString(
        $expected,
        json_encode($serializer)
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json\JsonML
     */
    public function testIntegrationWithEmptyDocument(): void {
      $serializer = new JsonML(new \DOMDocument());
      $this->assertEquals(
        '[]', (string)$serializer
      );
    }

    public  static function provideExamples(): array {
      return [
        'Simple element' => [
          '["alice", "bob"]',
          '<alice>bob</alice>'
        ],
        'Simple element (cdata)' => [
          '["alice", " bob "]',
          '<alice><![CDATA[ bob ]]></alice>'
        ],
        'Number (int)' => [
          '["alice", 42]',
          '<alice>42</alice>'
        ],
        'Number (float)' => [
          '["alice", 42.21]',
          '<alice>42.21</alice>'
        ],
        'Boolean (true)' => [
          '["alice", true]',
          '<alice>true</alice>'
        ],
        'Boolean (false)' => [
          '["alice", false]',
          '<alice>FALSE</alice>'
        ],
        'Nested elements' => [
          '["alice",["bob","charlie"],["david","edgar"]]',
          '<alice><bob>charlie</bob><david>edgar</david></alice>'
        ],
        'Multiple elements at the same level' => [
          '["alice",["bob","charlie"],["bob","david"]]',
          '<alice><bob>charlie</bob><bob>david</bob></alice>'
        ],
        'Attributes' => [
          '["alice",{"charlie":"david"},"bob"]',
          '<alice charlie="david">bob</alice>'
        ],
        'Default namespace URI' => [
          '[
            "alice",
            {
              "xmlns" : "http://some-namespace"
            },
            "bob"
          ]',
          '<alice xmlns="http://some-namespace">bob</alice>'
        ],
        'Namespaces' => [
          '[
            "alice",
            {
              "xmlns" : "http://some-namespace",
              "xmlns:charlie" : "http://some-other-namespace"
            },
            "bob"
          ]',
          '<alice xmlns="http://some-namespace" xmlns:charlie="http://some-other-namespace">bob</alice>'
        ],
        'Elements with namespace prefixes.' => [
          '[
            "alice",
            {
              "xmlns": "http:\/\/some-namespace",
              "xmlns:charlie": "http:\/\/some-other-namespace"
            },
            " ",
            [
              "bob",
              "david"
            ],
            " ",
            [
              "charlie:edgar",
              "frank"
            ],
            " "
          ]',
          '<alice xmlns="http://some-namespace" xmlns:charlie="http://some-other-namespace">'.
          ' <bob>david</bob> <charlie:edgar>frank</charlie:edgar> </alice>'
        ]
      ];
    }
  }
}
