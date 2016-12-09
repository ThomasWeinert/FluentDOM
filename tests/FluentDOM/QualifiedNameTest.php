<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class QualifiedNameTest extends TestCase {

    public function setUp() {
      // disable caching limit
      QualifiedName::$cacheLimit = 0;
    }

    /**
     * @covers \FluentDOM\QualifiedName
     * @dataProvider dataProviderValidQualifiedNames
     */
    public function testIsQualifiedName($name) {
      $qualifiedName = new QualifiedName($name);
      $this->assertEquals($name, (string)$qualifiedName);
    }

    public static function dataProviderValidQualifiedNames() {
      return array(
        array('tag'),
        array('namespace:tag'),
        array('_:_'),
        array('_-_'),
        array('_'),
        array('html'),
        array('tag23'),
        array('sample-tag'),
        array('sampleTag'),
        array('ns:tag'),
        array('ns:tag')
      );
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testIsQnameWithEmptyNameExpectingException() {
      $this->expectException(
        \UnexpectedValueException::class
      );
      new QualifiedName('');
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testIsNCNameWithEmptyTagnameExpectingException() {
      $this->expectException(
        \UnexpectedValueException::class,
        'Invalid QName "nc:": Missing QName part.'
      );
      new QualifiedName('nc:');
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testIsNCNameWithInvalidTagnameCharExpectingException() {
      $this->expectException(
        \UnexpectedValueException::class,
        'Invalid QName "nc:ta<g>": Invalid character at index 5.'
      );
      new QualifiedName('nc:ta<g>');
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testIsNCNameWithInvalidPrefixCharExpectingException() {
      $this->expectException(
        \UnexpectedValueException::class,
        'Invalid QName "n<c>:tag": Invalid character at index 1.'
      );
      new QualifiedName('n<c>:tag');
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testIsNCNameWithInvalidTagnameStartingCharExpectingException() {
      $this->expectException(
        \UnexpectedValueException::class,
        'Invalid QName "nc:1tag": Invalid character at index 3.'
      );
      new QualifiedName('nc:1tag');
    }

    /**
     * This is an integration test for the transparent caching.
     * With the low limit all parts of the logic will be triggered.
     *
     * @covers \FluentDOM\QualifiedName
     */
    public function testCaching() {
      QualifiedName::$cacheLimit = 3;
      $names = ['one', 'two', 'one', 'three', 'four', 'five', 'one'];
      foreach ($names as $name) {
        $this->assertTrue(QualifiedName::validate($name));
      }
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testPropertiesWithNCName() {
      $qualifiedName = new QualifiedName('tag');
      $this->assertEquals('tag', $qualifiedName->localName);
      $this->assertEquals('tag', $qualifiedName->name);
      $this->assertEquals('', $qualifiedName->prefix);
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testPropertiesWithFullName() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertEquals('tag', $qualifiedName->localName);
      $this->assertEquals('ns:tag', $qualifiedName->name);
      $this->assertEquals('ns', $qualifiedName->prefix);
    }

    /**
     * @covers \FluentDOM\QualifiedName
     * @dataProvider providePropertyNames
     */
    public function testPropertiesExistsExpectingTrue($property) {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertTrue(isset($qualifiedName->$property));
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testPropertiesExistsExpectingFalse() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertFalse(isset($qualifiedName->invalidPropertyName));
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testPropertyGetWithInvalidPropertyExpectingException() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->expectException(\LogicException::class);
      $qualifiedName->invalidPropertyName;
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testPropertySetExpectingException() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->expectException(\LogicException::class);
      $qualifiedName->name = 'foo';
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testPropertyUnsetExpectingException() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->expectException(\LogicException::class);
      unset($qualifiedName->name);
    }

    /**
     * @covers \FluentDOM\QualifiedName
     * @dataProvider provideQualifiedNamesForSplit
     */
    public function testSplit($expected, $name) {
      $this->assertSame(
        $expected, QualifiedName::split($name)
      );
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testValidateExceptionTrue() {
      $this->assertTrue(
        QualifiedName::validate('foo')
      );
    }

    /**
     * @covers \FluentDOM\QualifiedName
     */
    public function testValidateExceptionFalse() {
      $this->assertFalse(
        QualifiedName::validate('123')
      );
    }

    /**
     * @covers \FluentDOM\QualifiedName
     * @dataProvider provideStringsToNormalize
     */
    public function testNormalizeString($expected, $string) {
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
    public static function providePropertyNames() {
      return [
        ['name'],
        ['prefix'],
        ['localName'],
      ];
    }

    /**
     * @return array
     */
    public static function provideQualifiedNamesForSplit() {
      return array(
        array(array('foo', 'bar'), 'foo:bar'),
        array(array(FALSE, 'bar'), 'bar'),
        array(array('', 'bar'), ':bar'),
      );
    }

    public static function provideStringsToNormalize() {
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
