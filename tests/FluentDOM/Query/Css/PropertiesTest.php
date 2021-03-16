<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query\Css {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class PropertiesTest extends TestCase {

    /**
     * @covers \FluentDOM\Query\Css\Properties::__construct
     */
    public function testConstructor() {
      $css = new Properties('width: auto;');
      $this->assertEquals(
        ['width' => 'auto'], iterator_to_array($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::__toString
     */
    public function testMagicMethodToString() {
      $css = new Properties('width: auto;');
      $this->assertEquals(
        'width: auto;', (string)$css
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::offsetGet
     */
    public function testOffsetGet() {
      $css = new Properties('width: auto;');
      $this->assertEquals(
        'auto', $css['width']
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::offsetExists
     */
    public function testOffsetExistsExpectingTrue() {
      $css = new Properties('width: auto;');
      $this->assertTrue(isset($css['width']));
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::offsetExists
     */
    public function testOffsetExistsExpectingFalse() {
      $css = new Properties('width: auto;');
      $this->assertFalse(isset($css['height']));
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::offsetSet
     * @covers \FluentDOM\Query\Css\Properties::_isCssProperty
     */
    public function testOffsetSet() {
      $css = new Properties();
      $css['width'] = 'auto';
      $this->assertEquals(
        ['width' => 'auto'], iterator_to_array($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::offsetSet
     * @covers \FluentDOM\Query\Css\Properties::_isCssProperty
     */
    public function testOffsetSetWithInvalidName() {
      $css = new Properties();
      $this->expectException(\InvalidArgumentException::class);
      $css['---'] = 'test';
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::offsetSet
     */
    public function testOffsetSetWithEmptyValue() {
      $css = new Properties('width: auto; height: auto;');
      $css['width'] = '';
      $this->assertEquals('height: auto;', (string)$css);
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::offsetUnset
     */
    public function testOffsetUnset() {
      $css = new Properties('width: auto; height: auto;');
      unset($css['width']);
      $this->assertEquals('height: auto;', (string)$css);
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::offsetUnset
     */
    public function testOffsetUnsetWithArray() {
      $css = new Properties('width: auto; height: auto;');
      $names = ['width', 'height'];
      /** @noinspection PhpIllegalArrayKeyTypeInspection */
      unset($css[$names]);
      $this->assertEquals('', (string)$css);
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::setStyleString
     * @dataProvider provideStyleStrings
     */
    public function testSetStyleString($expected, $styleString) {
      $css = new Properties();
      $css->setStyleString($styleString);
      $this->assertEquals(
        $expected, iterator_to_array($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::getStyleString
     * @dataProvider providePropertyArrays
     */
    public function testGetStyleString($expected, $propertyArray) {
      $css = new Properties();
      foreach ($propertyArray as $name => $value) {
        $css[$name] = $value;
      }
      $this->assertEquals(
        $expected, $css->getStyleString()
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::getIterator
     */
    public function testGetIterator() {
      $css = new Properties('width: auto; height: auto;');
      $this->assertEquals(
        ['width' => 'auto', 'height' => 'auto'],
        iterator_to_array($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::count
     */
    public function testCountExpectingZero() {
      $css = new Properties('');
      $this->assertEquals(
        0, count($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::count
     */
    public function testCountExpectingTwo() {
      $css = new Properties('width: auto; height: auto;');
      $this->assertEquals(
        2, count($css)
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::compileValue
     */
    public function testCompileValueWithIntegerExpectingString() {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('sample'));
      $css = new Properties('');
      $this->assertSame(
        '42',
        $css->compileValue(
          42,
          $document->documentElement,
          23,
          'success'
        )
      );
    }

    /**
     * @covers \FluentDOM\Query\Css\Properties::compileValue
     */
    public function testCompileValueWithCallback() {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('sample'));
      $css = new Properties('');
      $this->assertSame(
        'success',
        $css->compileValue(
          [$this, 'callbackForCompileValue'],
          $document->documentElement,
          23,
          'success'
        )
      );
    }

    public function callbackForCompileValue($node, $index, $value) {
      $this->assertInstanceOf(\DOMElement::class, $node);
      $this->assertEquals(23, $index);
      return $value;
    }

    /********************
     * data provider
     ********************/

    public static function provideStyleStrings() {
      return [
        'single property' => [
          ['width' => 'auto'],
          'width: auto;'
        ]
      ];
    }

    public static function providePropertyArrays() {
      return [
        'single property' => [
          'width: auto;',
          ['width' => 'auto']
        ],
        'two properties' => [
          'height: auto; width: auto;',
          ['width' => 'auto', 'height' => 'auto']
        ],
        'detailed properties' => [
          'margin: 0; margin-top: 10px;',
          ['margin-top' => '10px', 'margin' => '0']
        ],
        'browser properties' => [
          'box-sizing: border-box; -moz-box-sizing: border-box; -o-box-sizing: border-box;',
          [
            '-o-box-sizing' => 'border-box',
            'box-sizing' => 'border-box',
            '-moz-box-sizing' => 'border-box'
          ]
        ]
      ];
    }
  }
}
