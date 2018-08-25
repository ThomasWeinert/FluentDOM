<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class BadgerFishTest extends TestCase {


    /**
     * Test against the examples from the BadgerFish webpage
     * http://badgerfish.ning.com/
     *
     * @covers \FluentDOM\Loader\Json\BadgerFish
     * @dataProvider provideExamples
     * @param string $json
     * @param string $xml
     * @throws \Exception
     * @throws \FluentDOM\Exceptions\InvalidSource
     */
    public function testIntegration($json, $xml) {
      $loader = new BadgerFish();
      $document = $loader->load($json, 'badgerfish');
      $this->assertXmlStringEqualsXmlString(
        $xml, $document->saveXML()
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

    /**
     * @covers \FluentDOM\Loader\Json\BadgerFish
     */
    public function testLoadFragment() {
      $loader = new BadgerFish();
      $this->assertXmlStringEqualsXmlString(
        '<alice>bob</alice>',
        $loader->loadFragment(
          '{"alice":{"$":"bob"}}',
          'badgerfish'
        )->saveXmlFragment()
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
