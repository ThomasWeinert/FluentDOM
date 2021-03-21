<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  /**
   * @covers \FluentDOM\DOM\Attribute
   */
  class AttributeTest extends TestCase {

    public function testMagicMethodToString(): void {
      $document = new Document();
      $document->appendElement('test', ['attr' => 'success']);
      $attribute = $document->documentElement->attributes->getNamedItem('attr');
      $this->assertEquals(
        'success',
        (string)$attribute
      );
    }

    public function testClarkNotation(): void {
      $document = new Document();
      $document->registerNamespace('a', 'urn:a');
      $document->appendElement('foo', ['a:bar' => '42']);
      $this->assertEquals(
        '{urn:a}bar',
        $document->documentElement->getAttributeNode('a:bar')->clarkNotation()
      );
    }

    public function testClarkNotationForEmptyNamespace(): void {
      $document = new Document();
      $document->appendElement('foo', ['bar' => '42']);
      $this->assertEquals(
        'bar',
        $document->documentElement->getAttributeNode('bar')->clarkNotation()
      );
    }
  }
}
