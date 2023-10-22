<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\XDM {

  use FluentDOM\Loader\StandardLoaders;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  /**
   * @covers \FluentDOM\Loader\XDM\XDMLoaders
   */
  class XDMLoadersTest extends TestCase {

    /**
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype): void {
      $loader = new XDMLoaders();
      $this->assertTrue($loader->supports($mimetype));
    }

    public static function provideSupportedTypes(): array {
      return [
        ['xdm-json'],
        ['application/xdm-json'],
        ['text/xdm-json']
      ];
    }
  }
}
