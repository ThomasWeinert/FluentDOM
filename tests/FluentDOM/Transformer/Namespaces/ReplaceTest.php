<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Transformer\Namespaces {

  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class ReplaceTest extends TestCase {

    /**
     * @covers \FluentDOM\Transformer\Namespaces\Replace
     * @dataProvider provideOptimizeExamples
     * @param string $expected
     * @param string $xml
     * @param array $namespaces
     * @param array $prefixes
     * @throws UnattachedNode
     */
    public function testReplace(
      string $expected, string $xml, array $namespaces, array $prefixes = []
    ): void {
      $document = new \DOMDocument();
      $document->loadXML($xml);
      $replace = new Replace($document, $namespaces, $prefixes);
      $this->assertXmlStringEqualsXmlString(
        $expected, (string)$replace
      );
    }

    public static function provideOptimizeExamples(): array {
      return [
        'Remove namespace' => [
          '<root />',
          '<foo:root xmlns:foo="urn:foo"/>',
          [
            'urn:foo' => ''
          ]
        ],
        'Add namespace' => [
          '<root xmlns="urn:foo"/>',
          '<root />',
          [
            '' => 'urn:foo'
          ]
        ],
        'Replace namespace' => [
          '<foo:root xmlns:foo="urn:bar"/>',
          '<foo:root xmlns:foo="urn:foo"/>',
          [
            'urn:foo' => 'urn:bar'
          ]
        ],
        'Replace namespace and prefix' => [
          '<b:root xmlns:b="urn:bar"/>',
          '<foo:root xmlns:foo="urn:foo"/>',
          [
            'urn:foo' => 'urn:bar'
          ],
          [
            'urn:bar' => 'b'
          ]
        ],
        'Remove namespace on attribute' => [
          '<root attr="value"/>',
          '<foo:root xmlns:foo="urn:foo" foo:attr="value"/>',
          [
            'urn:foo' => ''
          ]
        ],
        'Do not add namespace to attributes without prefix' => [
          '<root xmlns="urn:foo" attr="value"/>',
          '<root attr="value"/>',
          [
            '' => 'urn:foo'
          ]
        ],
        'Replace namespace on attributes' => [
          '<root xmlns:foo="urn:bar" foo:attr="value"/>',
          '<root xmlns:foo="urn:foo" foo:attr="value"/>',
          [
            'urn:foo' => 'urn:bar'
          ]
        ],
        'Replace namespace and prefix on attributes' => [
          '<root xmlns:b="urn:bar" b:attr="value"/>',
          '<root xmlns:foo="urn:foo" foo:attr="value"/>',
          [
            'urn:foo' => 'urn:bar'
          ],
          [
            'urn:bar' => 'b'
          ]
        ],
        'Copy text nodes' => [
          '<foo:root xmlns:foo="urn:bar" foo:attr="value">TEXT</foo:root>',
          '<foo:root xmlns:foo="urn:foo" foo:attr="value">TEXT</foo:root>',
          [
            'urn:foo' => 'urn:bar'
          ]
        ],
        'Copy child nodes' => [
          '<foo:root xmlns:foo="urn:bar" foo:attr="value"><foo:child/>TEXT</foo:root>',
          '<foo:root xmlns:foo="urn:foo" foo:attr="value"><foo:child/>TEXT</foo:root>',
          [
            'urn:foo' => 'urn:bar'
          ]
        ],
      ];
    }

  }
}
