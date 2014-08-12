<?php


namespace FluentDOM\Loader\Supports {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class Json_TestProxy {

    use Json;

    public function getSource($source) {
      return $this->getJson($source);
    }
  }

  class JsonTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithArray() {
      $loader = new Json_TestProxy();
      $this->assertEquals(['foo'], $loader->getSource(json_encode(['foo'])));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithObject() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource(json_encode($json)));
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
    public function testGetSourceWithInvalidJsonExpectingException() {
      $loader = new Json_TestProxy();
      $this->setExpectedException('FluentDOM\Exceptions\JsonError');
      $loader->getSource('{invalid');
    }
  }
}