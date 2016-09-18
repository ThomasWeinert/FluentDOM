<?php
namespace FluentDOM\Loader\Json {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class BadgerFishTest extends TestCase {


    /**
     * Test against the examples from the BadgerFish webpage
     * http://badgerfish.ning.com/
     *
     * @covers \FluentDOM\Loader\Json\BadgerFish
     * @dataProvider provideExamples
     * @param string $json
     * @param string $xml
     */
    public function testIntegeration($json, $xml) {
      $loader = new BadgerFish();
      $dom = $loader->load($json, 'badgerfish');
      $this->assertXmlStringEqualsXmlString(
        $xml, $dom->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\BadgerFish
     */
    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new BadgerFish();
      $this->assertNull(
        $loader->load(
          NULL,
          'badgerfish'
        )
      );
    }

    public  static function provideExamples() {
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
        ]
      ];
    }
  }
}