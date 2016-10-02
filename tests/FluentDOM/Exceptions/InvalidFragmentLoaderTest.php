<?php

namespace FluentDOM\Exceptions {

  require_once(__DIR__.'/../TestCase.php');


  class InvalidFragmentLoaderTest extends \PHPUnit_Framework_TestCase {

    public function testConstructor() {
      $exception = new InvalidFragmentLoader('LoaderClass');
      $this->assertEquals(
        'Loader "LoaderClass" can not load fragments.', $exception->getMessage()
      );
    }
  }
}
