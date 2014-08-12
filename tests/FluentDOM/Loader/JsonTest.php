<?php
namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class JsonTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Json
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype) {
      $loader = new Json();
      $this->assertTrue($loader->supports($mimetype));
    }

    /**
     * @covers FluentDOM\Loader\Json
     * @dataProvider provideSupportedTypes
     */
    public function testGetReturnLoadable($mimetype) {
      $loader = new Json();
      $this->assertInstanceOf('FluentDOM\Loadable', $loader->get($mimetype));
    }

    public static function provideSupportedTypes() {
      return [
        ['json'],
        ['jsonml'],
        ['badgerfish']
      ];
    }
  }
}