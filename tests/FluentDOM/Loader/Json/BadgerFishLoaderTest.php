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

  /**
   * @covers \FluentDOM\Loader\Json\BadgerFishLoader
   */
  class BadgerFishLoaderTest extends TestCase {


    /**
     * Test against the examples from the BadgerFish webpage
     * http://badgerfish.ning.com/
     *
     * @dataProvider provideExamples
     * @throws \Exception
     * @throws InvalidSource
     */
    public function testIntegration(string $json, string $xml): void {
      $loader = new BadgerFishLoader();
      $document = $loader->load($json, 'badgerfish')->getDocument();
      $this->assertXmlStringEqualsXmlString(
        $xml, $document->saveXML()
      );
    }

    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new BadgerFishLoader();
      $this->assertNull(
        $loader->load(
          NULL,
          'badgerfish'
        )
      );
    }

    public function testLoadFragment(): void {
      $loader = new BadgerFishLoader();
      $this->assertXmlStringEqualsXmlString(
        '<alice>bob</alice>',
        $loader->loadFragment(
          '{"alice":{"$":"bob"}}',
          'badgerfish'
        )->saveXmlFragment()
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
        'Attributes with namespace' => [
          '{"alice":{"@xmlns":{"$":"http:\\/\\/some-namespace","charlie":"http:\\/\\/some-other-namespace"},'.
          '"charlie:bob": {"@charlie:doris": 42}}}',
          '<alice xmlns="http://some-namespace" xmlns:charlie="http://some-other-namespace">'.
          '<charlie:bob charlie:doris="42"/>'.
          '</alice>'
        ]
      ];
    }
  }
}