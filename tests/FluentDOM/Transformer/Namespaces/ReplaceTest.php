<?php
/**
 * Allow an object to be appendable to a FluentDOM\Element
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Transformer\Namespaces {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class ReplaceTest extends TestCase {

    /**
     * @covers \FluentDOM\Transformer\Namespaces\Replace
     * @dataProvider provideOptimizeExamples
     */
    public function testReplace($expected, $xml, $namespaces) {
      $document = new \DOMDocument();
      $document->loadXml($xml);
      $replace = new Replace($document, $namespaces);
      $this->assertXmlStringEqualsXmlString(
        $expected, (string)$replace
      );
    }

    public static function provideOptimizeExamples() {
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