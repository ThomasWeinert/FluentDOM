<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class StandardTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Standard
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype) {
      $loader = new Standard();
      $this->assertTrue($loader->supports($mimetype));
    }

    public static function provideSupportedTypes() {
      return [
        ['xml'],
        ['text/xml'],
        ['application/xml'],
        ['html'],
        ['text/html'],
        ['json'],
        ['text/json'],
        ['application/json']
      ];
    }
  }
}