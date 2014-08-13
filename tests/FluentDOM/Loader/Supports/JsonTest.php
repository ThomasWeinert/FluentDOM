<?php


namespace FluentDOM\Loader\Supports {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class Json_TestProxy {

    use Json;

    public function getSupported() {
      return ['json'];
    }

    public function getSource($source, $type = 'json') {
      return $this->getJson($source, $type);
    }

    public function getValue($json) {
      return $this->getValueAsString($json);
    }
  }

  class JsonTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithArrayAsString() {
      $loader = new Json_TestProxy();
      $this->assertEquals(['foo'], $loader->getSource(json_encode(['foo'])));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithObjectAsString() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource(json_encode($json)));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithObject() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource($json));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithFile() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource(__DIR__.'/TestData/loader.json'));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithUnsupportedTypeExpectingFalse() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertFalse($loader->getSource($json, 'invalid'));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithInvalidJsonExpectingException() {
      $loader = new Json_TestProxy();
      $this->setExpectedException('FluentDOM\Exceptions\JsonError');
      $loader->getSource('{invalid');
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     * @dataProvider provideJsonValues
     */
    public function testGetValueAsJson($expected, $value) {
      $loader = new Json_TestProxy();
      $this->assertSame(
        $expected,
        $loader->getValue($value)
      );
    }

    public static function provideJsonValues() {
      return [
        ['true', TRUE],
        ['false', FALSE],
        ['', ''],
        ['foo', 'foo'],
        ['42', 42],
        ['42.21', 42.21]
      ];
    }
  }
}