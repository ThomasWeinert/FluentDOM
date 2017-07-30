<?php
namespace FluentDOM\Utility {

  require_once __DIR__ . '/../TestCase.php';

  use FluentDOM\TestCase;

  class NamespacesTest extends TestCase {

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testConstructorWithNamespaces() {
      $namespaces = new Namespaces(['foo' => 'urn:foo']);
      $this->assertEquals(
        ['foo' => 'urn:foo'],
        iterator_to_array($namespaces)
      );
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testGetNamespaceAfterRegister() {
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
    public function testGetDefaultNamespaceAfterRegister() {
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
    public function testGetDefaultNamespaceWithoutRegister() {
      $namespaces = new Namespaces();
      $this->assertEquals(
        '',
        $namespaces->resolveNamespace('#default')
      );
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testRegisterReservedNamespaceExpectingException() {
      $namespaces = new Namespaces();
      $this->expectException(
        \LogicException::class,
        'Can not register reserved namespace prefix "xml".'
      );
      $namespaces['xml'] = 'urn:fail';
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testGetReservedNamespace() {
      $namespaces = new Namespaces();
      $this->assertEquals(
        'http://www.w3.org/XML/1998/namespace',
        $namespaces->resolveNamespace('xml')
      );
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testGetNamespaceWithoutRegisterExpectingException() {
      $namespaces = new Namespaces();
      $this->expectException(
        \LogicException::class,
        'Unknown namespace prefix "test".'
      );
      $namespaces->resolveNamespace('test');
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testUnsetNamespacePrefix() {
      $namespaces = new Namespaces(['foo' => 'urn:foo']);
      unset($namespaces['foo']);
      $this->assertFalse(isset($namespaces['foo']));
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testCount() {
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
    public function testIsReservedPrefixExpectingTrue() {
      $namespaces = new Namespaces();
      $this->assertTrue($namespaces->isReservedPrefix('xml'));
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces
     */
    public function testIsReservedPrefixExpectingFalse() {
      $namespaces = new Namespaces();
      $this->assertFalse($namespaces->isReservedPrefix('prefix'));
    }

    /**
     * @covers \FluentDOM\Utility\Namespaces::store()
     * @covers \FluentDOM\Utility\Namespaces::restore()
     */
    public function testStoreStatusAndRestore() {
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
