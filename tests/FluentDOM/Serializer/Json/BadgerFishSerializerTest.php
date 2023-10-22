<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Serializer\Json {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class BadgerFishSerializerTest extends TestCase {

    /**
     * Test against the examples from the BadgerFishSerializer webpage
     * http://badgerfish.ning.com/
     *
     * @covers \FluentDOM\Serializer\Json\BadgerFishSerializer
     * @dataProvider provideExamples
     * @param string $expected
     * @param string $xml
     */
    public function testIntegration(string $expected, string $xml): void {
      $document = new \DOMDocument();
      $document->loadXML($xml);
      $serializer = new BadgerFishSerializer($document);
      $this->assertJsonStringEqualsJsonString(
        $expected,
        (string)$serializer
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Json\BadgerFishSerializer
     */
    public function testIntegrationWithEmptyDocument(): void {
      $serializer = new BadgerFishSerializer(new \DOMDocument());
      $this->assertEquals(
        '{}', (string)$serializer
      );
    }

    public  static function provideExamples(): array {
      return [
        'Text content of elements goes in the $ property of an object.' => [
          '{"alice":{"$":"bob"}}',
          '<alice>bob</alice>'
        ],
        'Nested elements become nested properties' => [
          '{"alice":{"bob":{"$":"charlie"},"david":{"$":"edgar"}}}',
          '<alice><bob>charlie</bob><david>edgar</david></alice>'
        ],
        'Multiple elements at the same level become array elements.' => [
          '{"alice":{"bob":[{"$":"charlie"},{"$":"david"}]}}',
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
        'Multiple text nodes and cdata section will be collected int $' => [
          '{"alice":{"$":"one  two "}}',
          '<alice>one <![CDATA[ two ]]></alice>'
        ]
      ];
    }
  }
}
