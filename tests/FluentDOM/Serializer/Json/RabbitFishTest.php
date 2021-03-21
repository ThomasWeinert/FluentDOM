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

  class RabbitFishTest extends TestCase {

    /**
     * @covers \FluentDOM\Serializer\Json\RabbitFish
     * @dataProvider provideExamples
     * @param string $expected
     * @param string $xml
     */
    public function testIntegration($expected, $xml) {
      $document = new \DOMDocument();
      $document->loadXML($xml);
      $serializer = new RabbitFish($document);
      $this->assertJsonStringEqualsJsonString(
        $expected,
        (string)$serializer
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json\RabbitFish
     */
    public function testIntegrationWithEmptyDocument(): void {
      $serializer = new RabbitFish(new \DOMDocument());
      $this->assertEquals(
        '{}', (string)$serializer
      );
    }

    public  static function provideExamples() {
      return [
        'Text content of elements' => [
          '{"alice": "bob" }',
          '<alice>bob</alice>'
        ],
        'Nested elements become nested properties' => [
          '{"alice":{"bob": "charlie","david":"edgar"}}',
          '<alice><bob>charlie</bob><david>edgar</david></alice>'
        ],
        'Multiple elements at the same level become array elements.' => [
          '{"alice":{"bob":["charlie", "david"]}}',
          '<alice><bob>charlie</bob><bob>david</bob></alice>'
        ],
        'Attributes go in properties whose names begin with @.' => [
          '{"alice":{"$":"bob","@charlie":"david"}}',
          '<alice charlie="david">bob</alice>'
        ],
        'The default namespace URI goes in @xmlns.$.' => [
          '{"alice":{"$":"bob","@xmlns":{"$":"http:\\/\\/some-namespace"}}}',
          '<alice xmlns="http://some-namespace">bob</alice>'
        ],
        'Other namespaces go in other properties of @xmlns' => [
          '{"alice":{"$":"bob","@xmlns":{"$":"http:\\/\\/some-namespace","charlie":"http:\\/\\/some-other-namespace"}}}',
          '<alice xmlns="http://some-namespace" xmlns:charlie="http://some-other-namespace">bob</alice>'
        ],
        'Elements with namespace prefixes become object properties, too.' => [
          '{"alice":{"bob":{"$":"david","@xmlns":{"$":"http:\\/\\/some-namespace",'.
          '"charlie":"http:\\/\\/some-other-namespace"}},"charlie:edgar":{"$":"frank","@xmlns":'.
          '{"charlie":"http:\\/\\/some-other-namespace"}},'.
          '"@xmlns":{"$":"http:\\/\\/some-namespace","charlie":"http:\\/\\/some-other-namespace"}}}',
          '<alice xmlns="http://some-namespace" xmlns:charlie="http://some-other-namespace">'.
          ' <bob>david</bob> <charlie:edgar>frank</charlie:edgar> </alice>'
        ],
        'Mixed content (element and text nodes) at the same level become array elements.' => [
          '{"alice":["bob",{"charlie":"david"},"edgar"]}',
          '<alice>bob<charlie>david</charlie>edgar</alice>'
        ],
        'Mixed content (element and text nodes) at the same level become array elements 2.' => [
          '{"alice":[{"@attribute":"yes"},"bob",{"charlie":"david"},"edgar"]}',
          '<alice attribute="yes">bob<charlie>david</charlie>edgar</alice>'
        ]
      ];
    }
  }
}
