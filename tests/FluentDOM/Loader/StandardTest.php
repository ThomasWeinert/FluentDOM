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

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class StandardTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Standard
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype): void {
      $loader = new Standard();
      $this->assertTrue($loader->supports($mimetype));
    }

    public static function provideSupportedTypes(): array {
      return [
        ['xml'],
        ['text/xml'],
        ['application/xml'],
        ['html'],
        ['text/html'],
        ['json'],
        ['text/json'],
        ['application/json'],
        ['php/simplexml']
      ];
    }
  }
}
