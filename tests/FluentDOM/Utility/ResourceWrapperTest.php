<?php
namespace FluentDOM\Utility {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class ResourceWrapperTest extends TestCase {

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testOpenStreamFromURI() {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      $outer = fopen(ResourceWrapper::createURI($inner), 'rb');
      $this->assertEquals('success', fread($outer, 100));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testOpenStreamFromContext() {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      list($uri, $context) = ResourceWrapper::createContext($inner);
      $outer = fopen($uri, 'rb', NULL, $context);
      $this->assertEquals('success', fread($outer, 100));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testUrlStat() {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      $this->assertInternalType('array', stat(ResourceWrapper::createURI($inner)));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testStreamRead() {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success_and_more'), 'rb');
      $outer = fopen(ResourceWrapper::createURI($inner), 'rb');
      $this->assertEquals('success', fread($outer, 7));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testStreamWriteAndSeek() {
      $inner = fopen('php://memory', 'wb+');
      $outer = fopen(ResourceWrapper::createURI($inner), 'wb+');
      fwrite($outer, 'success');
      fseek($outer, 0);
      $this->assertEquals('success', fread($inner, 7));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testStreamEof() {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      $outer = fopen(ResourceWrapper::createURI($inner), 'rb');
      $this->assertFalse(feof($outer));
      while (!feof($outer)) {
        fread($outer, 1000);
      }
      $this->assertTrue(feof($outer));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testOpenWithInvalidContext() {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      list($uri, $context) = ResourceWrapper::createContext($inner);
      $this->expectError(E_WARNING);
      fopen($uri, 'rb', NULL);
    }
  }
}
