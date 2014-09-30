<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class QualifiedNameTest extends TestCase {

    /**
     * @covers FluentDOM\QualifiedName
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
     * @covers FluentDOM\QualifiedName
     */
    public function testIsQnameWithEmptyNameExpectingException() {
      $this->setExpectedException(
        'UnexpectedValueException'
      );
      new QualifiedName('');
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testIsNCNameWithEmptyTagnameExpectingException() {
      $this->setExpectedException(
        'UnexpectedValueException',
        'Invalid QName "nc:": Missing QName part.'
      );
      new QualifiedName('nc:');
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testIsNCNameWithInvalidTagnameCharExpectingException() {
      $this->setExpectedException(
        'UnexpectedValueException',
        'Invalid QName "nc:ta<g>": Invalid character at index 5.'
      );
      new QualifiedName('nc:ta<g>');
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testIsNCNameWithInvalidTagnameStartingCharExpectingException() {
      $this->setExpectedException(
        'UnexpectedValueException',
        'Invalid QName "nc:1tag": Invalid character at index 3.'
      );
      new QualifiedName('nc:1tag');
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testPropertiesWithNCName() {
      $qualifiedName = new QualifiedName('tag');
      $this->assertEquals('tag', $qualifiedName->localName);
      $this->assertEquals('tag', $qualifiedName->name);
      $this->assertEquals('', $qualifiedName->prefix);
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testPropertiesWithFullName() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertEquals('tag', $qualifiedName->localName);
      $this->assertEquals('ns:tag', $qualifiedName->name);
      $this->assertEquals('ns', $qualifiedName->prefix);
    }

    /**
     * @covers FluentDOM\QualifiedName
     * @dataProvider providePropertyNames
     */
    public function testPropertiesExistsExpectingTrue($property) {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertTrue(isset($qualifiedName->$property));
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testPropertiesExistsExpectingFalse() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->assertFalse(isset($qualifiedName->invalidPropertyName));
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testPropertyGetWithInvalidPropertyExpectingException() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->setExpectedException('LogicException');
      $qualifiedName->invalidPropertyName;
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testPropertySetExpectingException() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->setExpectedException('LogicException');
      $qualifiedName->name = 'foo';
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testPropertyUnsetExpectingException() {
      $qualifiedName = new QualifiedName('ns:tag');
      $this->setExpectedException('LogicException');
      unset($qualifiedName->name);
    }

    /**
     * @covers FluentDOM\QualifiedName
     * @dataProvider provideQualifiedNamesForSplit
     */
    public function testSplit($expected, $name) {
      $this->assertSame(
        $expected, QualifiedName::split($name)
      );
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testValidateExceptionTrue() {
      $this->assertTrue(
        QualifiedName::validate('foo')
      );
    }

    /**
     * @covers FluentDOM\QualifiedName
     */
    public function testValidateExceptionFalse() {
      $this->assertFalse(
        QualifiedName::validate('123')
      );
    }

    /**
     * @covers FluentDOM\QualifiedName
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
