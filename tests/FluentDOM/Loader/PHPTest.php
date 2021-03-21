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

  class PHPTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\PHP
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype): void {
      $loader = new PHP();
      $this->assertTrue($loader->supports($mimetype));
    }

    /**
     * @covers \FluentDOM\Loader\PHP
     * @dataProvider provideSupportedTypes
     */
    public function testGetReturnLoadable($mimetype): void {
      $loader = new PHP();
      $this->assertInstanceOf(Loadable::class, $loader->get($mimetype));
    }

    public static function provideSupportedTypes() {
      return [
        ['php/pdo'],
        ['php/simplexml']
      ];
    }
  }
}
