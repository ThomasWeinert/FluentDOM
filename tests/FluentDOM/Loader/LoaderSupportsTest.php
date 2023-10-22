<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class Supports_TestProxy {
    use LoaderSupports;

    public function getSupported(): array {
      return ['type/example'];
    }
  }

  class Supports_TestProxyDefault {
    use LoaderSupports;
  }

  class LoaderSupportsTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testSupportsExpectingTrue(): void {
      $loader = new Supports_TestProxy();
      $this->assertTrue($loader->supports('type/example'));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testSupportsExpectingFalse(): void {
      $loader = new Supports_TestProxy();
      $this->assertFalse($loader->supports('type/another'));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderSupports
     */
    public function testSupportsDefaultExpectingFalse(): void {
      $loader = new Supports_TestProxyDefault();
      $this->assertFalse($loader->supports('type/another'));
    }
  }
}
