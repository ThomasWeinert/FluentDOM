<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class TextTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Text
     * @dataProvider provideSupportedTypes
     */
    public function testSupportsExpectingTrue($mimetype) {
      $loader = new Text();
      $this->assertTrue($loader->supports($mimetype));
    }

    /**
     * @covers FluentDOM\Loader\Text
     * @dataProvider provideSupportedTypes
     */
    public function testGetReturnLoadable($mimetype) {
      $loader = new Text();
      $this->assertInstanceOf('FluentDOM\Loadable', $loader->get($mimetype));
    }

    public static function provideSupportedTypes() {
      return [
        ['text/calendar'],
        ['text/csv']
      ];
    }
  }
}