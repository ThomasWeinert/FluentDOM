<?php
namespace FluentDOM\Loader\Json {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class SimpleXMLTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Json\SimpleXML
     * @dataProvider provideExamples
     * @param string $xmlInput
     * @param string $expectedXml
     */
    public function testIntegeration($xmlInput, $expectedXml) {
      $json = json_decode(json_encode(new \SimpleXMLElement($xmlInput)));
      $loader = new SimpleXML();
      $document = $loader->load($json, 'text/simplexml');
      $this->assertXmlStringEqualsXmlString(
        $expectedXml, $document->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Loader\Json\SimpleXML
     */
    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new SimpleXML();
      $this->assertNull(
        $loader->load(
          NULL,
          'text/simplexml'
        )
      );
    }

    public  static function provideExamples() {
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