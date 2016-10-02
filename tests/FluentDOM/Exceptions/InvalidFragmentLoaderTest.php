<?php

namespace FluentDOM\Exceptions;


class InvalidFragmentLoaderTest extends \PHPUnit_Framework_TestCase {

  public function testConstructor() {
    $exception = new InvalidFragmentLoader('LoaderClass');
    $this->assertEquals(
      'Loader "LoaderClass" can not load fragments.', $exception->getMessage()
    );
  }
}
