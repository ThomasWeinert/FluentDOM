<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../TestCase.php');

  class JSONxTest extends TestCase {


    /**
     * Test against the examples from the BadgerFish webpage
     * http://badgerfish.ning.com/
     *
     * @covers FluentDOM\Loader\JSONx
     * @dataProvider provideExamples
     * @param string $JsonDOM
     * @param string $JSONx
     */
    public function testIntegeration($JsonDOM, $JSONx) {
      $loader = new JSONx();
      $dom = $loader->load($JSONx, 'jsonx');
      $this->assertXmlStringEqualsXmlString(
        $JsonDOM, $dom->saveXml()
      );
    }

    public  static function provideExamples() {
      return [
        'object as root' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <Ticker>IBM</Ticker>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:string name="Ticker">IBM</json:string>
           </json:object>'
        ],
        'array as root' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013" json:type="array">
             <_>212 555-1111</_>
             <_>212 555-2222</_>
           </json:json>',
          '<json:array name="phoneNumbers" xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:string>212 555-1111</json:string>
             <json:string>212 555-2222</json:string>
           </json:array>'
        ],
        'object' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <Test>
               <Ticker>IBM</Ticker>
             </Test>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:object name="Test">
               <json:string name="Ticker">IBM</json:string>
             </json:object>
           </json:object>'
        ],
        'array' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <phoneNumbers json:type="array">
               <_>212 555-1111</_>
               <_>212 555-2222</_>
             </phoneNumbers>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:array name="phoneNumbers">
               <json:string>212 555-1111</json:string>
               <json:string>212 555-2222</json:string>
             </json:array>
           </json:object>'
        ],
        'boolean' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <remote json:type="boolean">false</remote>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:boolean name="remote">false</json:boolean>
           </json:object>'
        ],
        'string' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <name>John Smith</name>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
            <json:string name="name">John Smith</json:string>
           </json:object>'
        ],
        'number' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
            <height json:type="number">62.4</height>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:number name="height">62.4</json:number>
           </json:object>'
        ],
        'null' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <additionalInfo json:type="null"/>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:null name="additionalInfo" />
           </json:object>'
        ]
      ];
    }
  }
}