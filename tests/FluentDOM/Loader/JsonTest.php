<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loadable;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class JsonTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Json
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype): void {
      $loader = new Json();
      $this->assertTrue($loader->supports($mimetype));
    }

    /**
     * @covers \FluentDOM\Loader\Json
     * @dataProvider provideSupportedTypes
     */
    public function testGetReturnLoadable($mimetype): void {
      $loader = new Json();
      $this->assertInstanceOf(Loadable::class, $loader->get($mimetype));
    }

    public static function provideSupportedTypes() {
      return [
        ['json'],
        ['jsonml'],
        ['badgerfish'],
        ['jsonx']
      ];
    }
  }
}
