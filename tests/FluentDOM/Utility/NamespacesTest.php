<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Utility {

  require_once __DIR__ . '/../TestCase.php';

  use FluentDOM\TestCase;

  class NamespacesTest extends TestCase {

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testConstructorWithNamespaces(): void {
      $namespaces = new Namespaces(['foo' => 'urn:foo']);
      $this->assertEquals(
        ['foo' => 'urn:foo'],
        iterator_to_array($namespaces)
      );
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testGetNamespaceAfterRegister(): void {
      $namespaces = new Namespaces();
      $namespaces['test'] = 'urn:success';
      $this->assertEquals(
        'urn:success',
        $namespaces->resolveNamespace('test')
      );
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testGetDefaultNamespaceAfterRegister(): void {
      $namespaces = new Namespaces();
      $namespaces['#default'] = 'urn:success';
      $this->assertEquals(
        'urn:success',
        $namespaces->resolveNamespace('')
      );
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testGetDefaultNamespaceWithoutRegister(): void {
      $namespaces = new Namespaces();
      $this->assertEquals(
        '',
        $namespaces->resolveNamespace('#default')
      );
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testRegisterReservedNamespaceExpectingException(): void {
      $namespaces = new Namespaces();
      $this->expectException(\LogicException::class);
      $this->expectErrorMessage('Can not register reserved namespace prefix "xml".');
      $namespaces['xml'] = 'urn:fail';
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testGetReservedNamespace(): void {
      $namespaces = new Namespaces();
      $this->assertEquals(
        'http://www.w3.org/XML/1998/namespace',
        $namespaces->resolveNamespace('xml')
      );
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testGetNamespaceWithoutRegisterExpectingException(): void {
      $namespaces = new Namespaces();
      $this->expectException(\LogicException::class);
      $this->expectErrorMessage('Unknown namespace prefix "test".');
      $namespaces->resolveNamespace('test');
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testUnsetNamespacePrefix(): void {
      $namespaces = new Namespaces(['foo' => 'urn:foo']);
      unset($namespaces['foo']);
      $this->assertFalse(isset($namespaces['foo']));
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testCount(): void {
      $namespaces = new Namespaces(
        [
          'foo' => 'urn:foo',
          'bar' => 'urn:bar'
        ]
      );
      $this->assertCount(2, $namespaces);
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testIsReservedPrefixExpectingTrue(): void {
      $namespaces = new Namespaces();
      $this->assertTrue($namespaces->isReservedPrefix('xml'));
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testIsReservedPrefixExpectingFalse(): void {
      $namespaces = new Namespaces();
      $this->assertFalse($namespaces->isReservedPrefix('prefix'));
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces::store()
     * @covers \FluentDOM\Utility\Namespaces::restore()
     */
    public function testStoreStatusAndRestore(): void {
      $namespaces = new Namespaces();
      $namespaces['foo'] = 'urn:foo';
      $namespaces->store();
      $namespaces['foo'] = 'urn:bar';
      $this->assertEquals('urn:bar', $namespaces['foo']);
      $namespaces->restore();
      $this->assertEquals('urn:foo', $namespaces['foo']);
    }
  }
}
