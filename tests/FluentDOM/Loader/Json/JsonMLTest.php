<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class JsonMLTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Json\JsonML
     * @dataProvider provideExamples
     * @param string $json
     * @param string $xml
     * @throws InvalidSource
     */
    public function testIntegration(string $json, string $xml): void {
      $loader = new JsonML();
      $document = $loader->load($json, 'jsonml')->getDocument();
      $this->assertXmlStringEqualsXmlString(
        $xml, $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonML
     */
    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new JsonML();
      $this->assertNull(
        $loader->load(
          NULL,
          'jsonml'
        )
      );
    }

    public  static function provideExamples(): array {
      return [
        'Simple element' => [
          '["alice", "bob"]',
          '<alice>bob</alice>'
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
          '<alice>false</alice>'
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
        ],
        'Attributes with namespace prefixes.' => [
          '[
            "alice",
            {
              "xmlns": "http:\/\/some-namespace",
              "xmlns:charlie": "http:\/\/some-other-namespace"
            },
            " ",
            [
              "charlie:bob",
              {
                "charlie:doris": 42
              }
            ],
            " "
          ]',
          '<alice xmlns="http://some-namespace" xmlns:charlie="http://some-other-namespace">'.
          '<charlie:bob charlie:doris="42"/>'.
          '</alice>'
        ]
      ];
    }
  }
}
