<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Utility {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class ResourceWrapperTest extends TestCase {

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testOpenStreamFromURI(): void {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      $outer = fopen(ResourceWrapper::createURI($inner), 'rb');
      $this->assertEquals('success', fread($outer, 100));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testOpenStreamFromContext(): void {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      list($uri, $context) = ResourceWrapper::createContext($inner);
      $outer = fopen($uri, 'rb', FALSE, $context);
      $this->assertEquals('success', fread($outer, 100));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testUrlStat(): void {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      $this->assertIsArray(stat(ResourceWrapper::createURI($inner)));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testStreamRead(): void {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success_and_more'), 'rb');
      $outer = fopen(ResourceWrapper::createURI($inner), 'rb');
      $this->assertEquals('success', fread($outer, 7));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testStreamWriteAndSeek(): void {
      $inner = fopen('php://memory', 'wb+');
      $outer = fopen(ResourceWrapper::createURI($inner), 'wb+');
      fwrite($outer, 'success');
      fseek($outer, 0);
      $this->assertEquals('success', fread($inner, 7));
    }

    /**
     * @covers \FluentDOM\Utility\ResourceWrapper
     */
    public function testStreamEof(): void {
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
    public function testOpenWithInvalidContext(): void {
      $inner = fopen('data://text/plain;base64,'.base64_encode('success'), 'rb');
      [$uri] = ResourceWrapper::createContext($inner);
      try {
        $this->assertFalse(fopen($uri, 'rb', FALSE));
      } catch (\ErrorException $e) {
        $this->assertStringContainsString('Failed to open stream:', $e->getMessage());
      }
    }
  }
}
