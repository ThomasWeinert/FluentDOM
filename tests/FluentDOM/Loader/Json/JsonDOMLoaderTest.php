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
  use FluentDOM\Loader\LoaderOptions;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class JsonDOMLoaderTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testSupportsExpectingTrue(): void {
      $loader = new JsonDOMLoader();
      $this->assertTrue($loader->supports('json'));
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testSupportsExpectingFalse(): void {
      $loader = new JsonDOMLoader();
      $this->assertFalse($loader->supports('text/xml'));
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithValidJsonDOM(): void {
      $loader = new JsonDOMLoader();
      $document = $loader->load(
        '{"foo":"bar"}',
        'json'
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json xmlns:json="urn:carica-json-dom.2013">'.
        '<foo>bar</foo>'.
        '</json:json>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithValidFileAllowFile(): void {
      $loader = new JsonDOMLoader();
      $document = $loader->load(
        __DIR__.'/TestData/loader.json',
        'json',
        [
          LoaderOptions::ALLOW_FILE => TRUE
        ]
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json xmlns:json="urn:carica-json-dom.2013">'.
        '<foo>bar</foo>'.
        '</json:json>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithValidFileExpectingException(): void {
      $loader = new JsonDOMLoader();
      $this->expectException(InvalidSource\TypeFile::class);
      $loader->load(
        __DIR__.'/TestData/loader.json',
        'json'
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithValidStructure(): void {
      $loader = new JsonDOMLoader();
      $json = new \stdClass();
      $json->foo = 'bar';
      $document = $loader->load(
        $json, 'json'
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json xmlns:json="urn:carica-json-dom.2013">'.
        '<foo>bar</foo>'.
        '</json:json>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithValidJsonVerbose(): void {
      $loader = new JsonDOMLoader(JsonDOMLoader::OPTION_VERBOSE);
      $document = $loader->load(
        '{"foo":"bar"}',
        'json'
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json'.
        ' xmlns:json="urn:carica-json-dom.2013"'.
        ' json:type="object">'.
        '<foo json:name="foo" json:type="string">bar</foo>'.
        '</json:json>',
        $document->saveXml()
      );
    }
    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithDifferentDataTypes(): void {
      $loader = new JsonDOMLoader();
      $document = $loader->load(
        json_encode(
          [
            'boolean' => TRUE,
            'int' => 42,
            'null' => NULL,
            'string' => 'Foo',
            'array' => [21],
            'object' => new \stdClass()
          ]
        ),
        'json'
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>
         <json:json xmlns:json="urn:carica-json-dom.2013">
           <boolean json:type="boolean">true</boolean>
           <int json:type="number">42</int>
           <null json:type="null"/>
           <string>Foo</string>
           <array json:type="array">
             <_ json:type="number">21</_>
           </array>
           <object json:type="object"/>
         </json:json>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithAssociativeArray(): void {
      $loader = new JsonDOMLoader();
      $json = ['foo' => 'bar'];
      $document = $loader->load(
        $json, 'json'
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json xmlns:json="urn:carica-json-dom.2013">'.
        '<foo>bar</foo>'.
        '</json:json>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new JsonDOMLoader();
      $this->assertNull(
        $loader->load(
          NULL,
          'json'
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithInvalidJsonStringExpectingException(): void {
      $loader = new JsonDOMLoader();
      $this->expectException(\UnexpectedValueException::class);
      $loader->load(
        '{foo}}',
        'json'
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadStoppingAtMaxDepth(): void {
      $loader = new JsonDOMLoader(0, 1);
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>
         <json:json xmlns:json="urn:carica-json-dom.2013"><foo/></json:json>',
        $loader
          ->load(json_encode(['foo' => [1, 2, 3]]), 'json')
          ->getDocument()
          ->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithEmptyArray(): void {
      $loader = new JsonDOMLoader(0, 1);
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>
         <json:json xmlns:json="urn:carica-json-dom.2013" json:type="array"/>',
        $loader
          ->load('[]', 'json')
          ->getDocument()
          ->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOMLoader
     */
    public function testLoadWithArrayMappingTagName(): void {
      $loader = new JsonDOMLoader();
      $json = [
        'numbers' => [21, 42]
      ];
      $document = $loader->load(
        $json,
        'json',
        [
          JsonDOMLoader::ON_MAP_KEY => function($key, $isArrayElement) {
            return $isArrayElement ? 'number' : $key;
          }
        ]
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<json:json xmlns:json="urn:carica-json-dom.2013">'.
        '<numbers json:type="array">'.
          '<number json:type="number">21</number>'.
          '<number json:type="number">42</number>'.
        '</numbers>'.
        '</json:json>',
        $document->saveXml()
      );
    }

    public function testLoadFragmentWithString(): void {
      $loader = new JsonDOMLoader();
      $json = [
        'numbers' => [21, 42]
      ];
      $fragment = $loader->loadFragment(
        json_encode($json),
        'json',
        [
          JsonDOMLoader::ON_MAP_KEY => function($key, $isArrayElement) {
            return $isArrayElement ? 'number' : $key;
          }
        ]
      );
      $this->assertXmlStringEqualsXmlString(
        '<numbers xmlns:json="urn:carica-json-dom.2013" json:type="array">'.
        '<number json:type="number">21</number>'.
        '<number json:type="number">42</number>'.
        '</numbers>',
        $fragment->ownerDocument->saveXml($fragment)
      );
    }

    public function testLoadFragmentWithUnsupportedTypeExpectingNull(): void {
      $loader = new JsonDOMLoader();
      $this->assertNull($loader->loadFragment('', 'unknown'));
    }
  }
}
