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

  class RayfishLoaderTest extends TestCase {


    /**
     * @covers \FluentDOM\Loader\Json\RayfishLoader
     * @dataProvider provideExamples
     * @param string $json
     * @param string $xml
     * @throws InvalidSource
     */
    public function testIntegration(string $json, string $xml): void {
      $loader = new RayfishLoader();
      $document = $loader->load($json, 'Rayfish')->getDocument();
      $this->assertXmlStringEqualsXmlString(
        $xml, $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\RayfishLoader
     * @throws InvalidSource
     */
    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new RayfishLoader();
      $this->assertNull(
        $loader->load(
          NULL,
          'rayfish'
        )
      );
    }

    public  static function provideExamples(): array {
      return [
        'Simple element' => [
          '{ "#name": "alice", "#text": null, "#children": [ ] }',
          '<alice/>'
        ],
        'Nested element' => [
          '{ "#name": "alice", "#text": null, "#children": [
            { "#name": "bob", "#text": "charlie", "#children": [ ] },
            { "#name": "david", "#text": "edgar", "#children": [ ] }
          ]}',
          '<alice><bob>charlie</bob><david>edgar</david></alice>'
        ],
        'Attributes' => [
          '{ "#name": "alice", "#text": "bob", "#children": [
            { "#name": "@charlie",
              "#text": "david",
              "#children": [ ]
            }
          ]}',
          '<alice charlie="david">bob</alice>'
        ],
        'Default Namespace' => [
          '{ "#name": "alice", "#text": null, "#children": [
            { "#name": "@xmlns", "#text": "urn:bob", "#children": [ ]}
           ]}',
          '<alice xmlns="urn:bob"/>'
        ],
        'Namespace' => [
          '{ "#name": "charlie:alice", "#text": null, "#children": [
            { "#name": "@xmlns:charlie", "#text": "urn:bob", "#children": [ ]}
           ]}',
          '<charlie:alice xmlns:charlie="urn:bob"/>'
        ],
      ];
    }
  }
}
