<?php
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
    public function testSupportsExpectingTrue($mimetype) {
      $loader = new PHP();
      $this->assertTrue($loader->supports($mimetype));
    }

    /**
     * @covers \FluentDOM\Loader\PHP
     * @dataProvider provideSupportedTypes
     */
    public function testGetReturnLoadable($mimetype) {
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