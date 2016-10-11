<?php
namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class NoSerializerTest extends TestCase  {

    public function testConstructor() {
      $exception = new NoSerializer('some/type');
      $this->assertEquals(
        'No serializer for content type some/type available.',
        $exception->getMessage()
      );
    }
  }
}
