<?php


namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class Supports_TestProxy {
    use Supports;

    public function getSupported() {
      return ['type/example'];
    }
  }

  class Supports_TestProxyDefault {
    use Supports;
  }

  class SupportsTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Supports
     */
    public function testSupportsExpectingTrue() {
      $loader = new Supports_TestProxy();
      $this->assertTrue($loader->supports('type/example'));
    }

    /**
     * @covers \FluentDOM\Loader\Supports
     */
    public function testSupportsExpectingFalse() {
      $loader = new Supports_TestProxy();
      $this->assertFalse($loader->supports('type/another'));
    }

    /**
     * @covers \FluentDOM\Loader\Supports
     */
    public function testSupportsDefaultExpectingFalse() {
      $loader = new Supports_TestProxyDefault();
      $this->assertFalse($loader->supports('type/another'));
    }
  }
}