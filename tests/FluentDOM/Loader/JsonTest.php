<?php
namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loadable;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class JsonTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Json
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype) {
      $loader = new Json();
      $this->assertTrue($loader->supports($mimetype));
    }

    /**
     * @covers \FluentDOM\Loader\Json
     * @dataProvider provideSupportedTypes
     */
    public function testGetReturnLoadable($mimetype) {
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