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

  use FluentDOM\Loadable;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class PHPLoadersTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\PHPLoaders
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype): void {
      $loader = new PHPLoaders();
      $this->assertTrue($loader->supports($mimetype));
    }

    /**
     * @covers \FluentDOM\Loader\PHPLoaders
     * @dataProvider provideSupportedTypes
     */
    public function testGetReturnLoadable($mimetype): void {
      $loader = new PHPLoaders();
      $this->assertInstanceOf(Loadable::class, $loader->get($mimetype));
    }

    public static function provideSupportedTypes(): array {
      return [
        ['php/pdo'],
        ['php/simplexml']
      ];
    }
  }
}
