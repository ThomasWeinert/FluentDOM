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

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  /**
   * @covers \FluentDOM\Utility\QualifiedName
   */
  class QualifiedNameTest extends TestCase {

    public function setUp(): void {
      // disable caching limit
      QualifiedName::$cacheLimit = 0;
    }

    /**
     * @dataProvider dataProviderValidQualifiedNames
     * @param string $name
     */
    public function testIsQualifiedName(string $name): void {
      $qualifiedName = new QualifiedName($name);
      $this->assertEquals($name, (string)$qualifiedName);
    }

    public static function dataProviderValidQualifiedNames(): array {
      return [
        ['tag'],
        ['namespace:tag'],
        ['_:_'],
        ['_-_'],
        ['_'],
        ['html'],
        ['tag23'],
        ['sample-tag'],
        ['sampleTag'],
        ['ns:tag'],
        ['ns:tag']
      ];
    }

    public function testIsQnameWithEmptyNameExpectingException(): void {
      $this->expectException(
        \UnexpectedValueException::class
      );
      new QualifiedName('');
    }

    public function testIsNCNameWithEmptyTagNameExpectingException(): void {
      $this->expectException(\UnexpectedValueException::class);
      $this->expectDeprecationMessage('Invalid QName "nc:": Missing QName part.');
      new QualifiedName('nc:');
    }

    public function testIsNCNameWithInvalidTagNameCharExpectingException(): void {
      $this->expectException(\UnexpectedValueException::class);
      $this->expectExceptionMessage('Invalid QName "nc:ta<g>": Invalid character at index 5.');
      new QualifiedName('nc:ta<g>');
    }

    public function testIsNCNameWithInvalidPrefixCharExpectingException(): void {
      $this->expectException(\UnexpectedValueException::class);
      $this->expectExceptionMessage('Invalid QName "n<c>:tag": Invalid character at index 1.');
      new QualifiedName('n<c>:tag');
    }

    public function testIsNCNameWithInvalidTagNameStartingCharExpectingException(): void {
      $this->expectException(\UnexpectedValueException::class);
      $this->expectExceptionMessage('Invalid QName "nc:1tag": Invalid character at index 3.');
      new QualifiedName('nc:1tag');
    }

    /**
     * This is an integration test for the transparent caching.
     * With the low limit all parts of the logic will be triggered.
     */
    public function testCache(): void {
      QualifiedName::$cacheLimit = 3;
      $names = ['one', 'two', 'one', 'three', 'four', 'five', 'one'];
      foreach ($names as $name) {
        $this->assertTrue(QualifiedName::validate($name));
      }
    }

    public function testPropertiesWithNCName(): void {
      $qualifiedName = new QualifiedName('tag');
      $this->assertEquals('tag', $qualifiedName->localName);
      $this->assertEquals('tag', $qualifiedName->name);
      $this->assertEquals('', $qualifiedName->prefix);
    }

    public function testPropertiesWithFullName(): void {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertEquals('tag', $qualifiedName->localName);
      $this->assertEquals('ns:tag', $qualifiedName->name);
      $this->assertEquals('ns', $qualifiedName->prefix);
    }

    /**
     * @dataProvider providePropertyNames
     * @param string $property
     */
    public function testPropertiesExistsExpectingTrue(string $property): void {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertTrue(isset($qualifiedName->$property));
    }

    public function testPropertiesExistsExpectingFalse(): void {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertFalse(isset($qualifiedName->invalidPropertyName));
    }

    public function testPropertyGetWithInvalidPropertyExpectingException(): void {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->expectException(\LogicException::class);
      /** @noinspection PhpUndefinedFieldInspection */
      /** @noinspection PhpExpressionResultUnusedInspection */
      $qualifiedName->invalidPropertyName;
    }

    public function testPropertySetExpectingException(): void {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->expectException(\LogicException::class);
      $qualifiedName->name = 'foo';
    }

    public function testPropertyUnsetExpectingException(): void {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->expectException(\LogicException::class);
      unset($qualifiedName->name);
    }

    /**
     * @dataProvider provideQualifiedNamesForSplit
     * @param array $expected
     * @param string $name
     */
    public function testSplit(array $expected, string $name): void {
      $this->assertSame(
        $expected, QualifiedName::split($name)
      );
    }

    public function testValidateExceptionTrue(): void {
      $this->assertTrue(
        QualifiedName::validate('foo')
      );
    }

    public function testValidateExceptionFalse(): void {
      $this->assertFalse(
        QualifiedName::validate('123')
      );
    }

    /**
     * @dataProvider provideStringsToNormalize
     * @param string $expected
     * @param string $string
     */
    public function testNormalizeString(string $expected, string $string): void {
      $this->assertEquals(
        $expected,
        QualifiedName::normalizeString($string)
      );
    }

    /*************************
     * Data Provider
     ************************/

    /**
     * @return array
     */
    public static function providePropertyNames(): array {
      return [
        ['name'],
        ['prefix'],
        ['localName'],
      ];
    }

    /**
     * @return array
     */
    public static function provideQualifiedNamesForSplit(): array {
      return [
        [['foo', 'bar'], 'foo:bar'],
        [[FALSE, 'bar'], 'bar'],
        [['', 'bar'], ':bar'],
      ];
    }

    public static function provideStringsToNormalize(): array {
      return [
        ['foo', 'foo'],
        ['foo-bar', 'foo-bar'],
        ['fooBAR', 'foo:BAR'],
        ['foo', '  f o o   '],
        ['_', '  ']
      ];
    }
  }
}
