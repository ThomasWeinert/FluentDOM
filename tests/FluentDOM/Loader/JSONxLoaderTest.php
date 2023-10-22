<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class JSONxLoaderTest extends TestCase {


    /**
     * Test against the examples from the BadgerFishLoader webpage
     * http://badgerfish.ning.com/
     *
     * @covers \FluentDOM\Loader\JSONxLoader
     * @dataProvider provideExamples
     * @param string $JsonDOM
     * @param string $JSONx
     * @throws InvalidSource|\Throwable
     */
    public function testIntegration(string $JsonDOM, string $JSONx): void {
      $loader = new JSONxLoader();
      $document = $loader->load($JSONx, 'jsonx')->getDocument();
      $this->assertXmlStringEqualsXmlString(
        $JsonDOM, $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\JSONxLoader
     */
    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new JSONxLoader();
      $this->assertNull(
        $loader->load(
          NULL,
          'jsonx'
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\JSONxLoader
     */
    public function testLoadFromFileConvertToJson(): void {
      $loader = new JSONxLoader();
      $document = $loader->load(
        __DIR__.'/TestData/jsonx.xml',
        'jsonx',
        [LoaderOptions::IS_FILE => TRUE]
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>
          <json:json xmlns:json="urn:carica-json-dom.2013">
            <name>John Smith</name>
            <address>
              <streetAddress>21 2nd Street</streetAddress>
              <city>New York</city>
              <state>NY</state>
              <postalCode json:type="number">10021</postalCode>
            </address>
            <phoneNumbers json:type="array">
              <_>212 555-1111</_>
              <_>212 555-2222</_>
            </phoneNumbers>
            <additionalInfo json:type="null"/>
            <remote json:type="boolean">false</remote>
            <height json:type="number">62.4</height>
            <ficoScore>&gt; 640</ficoScore>
          </json:json>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\JSONxLoader
     */
    public function testLoadFragment(): void {
      $loader = new JSONxLoader();
      $fragment = $loader->loadFragment(
        '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx" name="Example">
             <json:string name="Ticker">IBM</json:string>
           </json:object>',
        'jsonx'
      );
      $this->assertXmlStringEqualsXmlString(
        '<Example>
             <Ticker>IBM</Ticker>
           </Example>',
        $fragment->saveXmlFragment()
      );
    }

    /**
     * @covers \FluentDOM\Loader\JSONxLoader
     * @throws \Throwable
     */
    public function testLoadFragmentWithInvalidSourceExpectingNull(): void {
      $loader = new JSONxLoader();
      $this->assertNull(
        $loader->loadFragment(
          NULL,
          'jsonx'
        )
      );
    }

    public  static function provideExamples(): array {
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
        ],
        'numeric name, needs name attribute' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <_ json:name="123" json:type="null"/>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:null name="123" />
           </json:object>'
        ],
        'empty object, needs type attribute' => [
          '<json:json xmlns:json="urn:carica-json-dom.2013">
             <additionalInfo json:type="object"/>
           </json:json>',
          '<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
             <json:object name="additionalInfo" />
           </json:object>'
        ]
      ];
    }
  }
}