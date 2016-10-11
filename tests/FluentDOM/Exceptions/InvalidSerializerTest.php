<?php
namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class InvalidSerializerTest extends TestCase  {

    public function testConstructor() {
      $exception = new InvalidSerializer('some/type', 'SerializerClass');
      $this->assertEquals(
        'Invalid serializer for content type some/type, instances of SerializerClass are not castable to string.',
        $exception->getMessage()
      );
    }
  }
}
