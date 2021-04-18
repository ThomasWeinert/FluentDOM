<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class SimpleXMLTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Json\SimpleXML
     * @dataProvider provideExamples
     * @param string $xmlInput
     * @param string $expectedXml
     * @throws InvalidSource
     */
    public function testIntegration(string $xmlInput, string $expectedXml): void {
      $json = json_decode(json_encode(new \SimpleXMLElement($xmlInput)), false);
      $loader = new SimpleXML();
      $document = $loader->load($json, 'text/simplexml')->getDocument();
      $this->assertXmlStringEqualsXmlString(
        $expectedXml, $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\SimpleXML
     */
    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new SimpleXML();
      $this->assertNull(
        $loader->load(
          NULL,
          'text/simplexml'
        )
      );
    }

    public  static function provideExamples(): array {
      return [
        'Simple element' => [
          '<alice><bob>text</bob></alice>',
          '<json:json xmlns:json="urn:carica-json-dom.2013"><bob>text</bob></json:json>'
        ],
        'Attributes' => [
          '<alice><bob charlie="dean"/></alice>',
          '<json:json xmlns:json="urn:carica-json-dom.2013"><bob charlie="dean"/></json:json>'
        ],
        'List' => [
          '<alice><bob>one</bob><bob>two</bob><bob>three</bob></alice>',
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <bob>one</bob>
             <bob>two</bob>
             <bob>three</bob>
           </json:json>'
        ],
      ];
    }
  }
}
