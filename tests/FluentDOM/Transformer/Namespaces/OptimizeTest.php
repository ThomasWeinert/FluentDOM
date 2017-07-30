<?php
/**
 * Allow an object to be appendable to a FluentDOM\Element
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Transformer\Namespaces {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class OptimizeTest extends TestCase {

    /**
     * @covers \FluentDOM\Transformer\Namespaces\Optimize
     * @dataProvider provideOptimizeExamples
     */
    public function testOptimize($expected, $xml, $namespaces = []) {
      $document = new \DOMDocument();
      $document->loadXml($xml);
      $optimize = new Optimize($document, $namespaces);
      $this->assertXmlStringEqualsXmlString(
        $expected, (string)$optimize
      );
    }

    public static function provideOptimizeExamples() {
      return [
        'Keep need namespace' => [
          '<foo:root xmlns:foo="urn:foo"/>',
          '<foo:root xmlns:foo="urn:foo"/>'
        ],
        'Add prefix for default namespace' => [
          '<foo:root xmlns:foo="urn:foo"/>',
          '<root xmlns="urn:foo"/>',
          [
            'urn:foo' => 'foo'
          ]
        ],
        'Change prefix for attribute' => [
          '<foo:root xmlns:foo="urn:foo" foo:attr="42"/>',
          '<bar:root xmlns:bar="urn:foo" bar:attr="42"/>',
          [
            'urn:foo' => 'foo'
          ]
        ],
        'Remove prefix for a namespace' => [
          '<root xmlns="urn:foo"/>',
          '<foo:root xmlns:foo="urn:foo"/>',
          [
            'urn:foo' => ''
          ]
        ],
        'Move namespace definition to parent' => [
          '<foo:root xmlns:foo="urn:foo" xmlns:bar="urn:bar"><bar:child /></foo:root>',
          '<foo:root xmlns:foo="urn:foo"><bar:child xmlns:bar="urn:bar"/></foo:root>'
        ],
        'Move namespace definition to ancestor' => [
          '<foo:root xmlns:foo="urn:foo" xmlns:bar="urn:bar"><foo:child><bar:child/></foo:child></foo:root>',
          '<foo:root xmlns:foo="urn:foo"><foo:child><bar:child xmlns:bar="urn:bar"/></foo:child></foo:root>'
        ],
        'Copy text nodes' => [
          '<root xmlns="urn:foo">bar</root>',
          '<root xmlns="urn:foo">bar</root>'
        ],
        'Without a namespace' => [
          '<root attr="value">bar</root>',
          '<root attr="value">bar</root>'
        ],
        "Reset default namespace to empty" => [
          '<root xmlns="urn:foo"><div xmlns=""/></root>',
          '<foo:root xmlns:foo="urn:foo"><div/></foo:root>',
          [
            'urn:foo' => ''
          ]
        ],
        "Remove all namespace prefix of child node while parent has no namespace" => [
          '<root><child xmlns="urn:bar"/></root>',
          '<root><bar:child xmlns:bar="urn:bar"/></root>',
          [
            'urn:foo' => '',
            'urn:bar' => ''
          ]
        ]
      ];
    }

  }
}