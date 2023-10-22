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

  class JsonLoadersTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\JsonLoaders
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype): void {
      $loader = new JsonLoaders();
      $this->assertTrue($loader->supports($mimetype));
    }

    /**
     * @covers \FluentDOM\Loader\JsonLoaders
     * @dataProvider provideSupportedTypes
     */
    public function testGetReturnLoadable($mimetype): void {
      $loader = new JsonLoaders();
      $this->assertInstanceOf(Loadable::class, $loader->get($mimetype));
    }

    public static function provideSupportedTypes(): array {
      return [
        ['json'],
        ['jsonml'],
        ['badgerfish'],
        ['jsonx']
      ];
    }
  }
}
