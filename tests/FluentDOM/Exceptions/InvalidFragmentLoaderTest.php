<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';


  class InvalidFragmentLoaderTest extends TestCase  {

    public function testConstructor() {
      $exception = new InvalidFragmentLoader('LoaderClass');
      $this->assertEquals(
        'Loader "LoaderClass" can not load fragments.', $exception->getMessage()
      );
    }
  }
}
