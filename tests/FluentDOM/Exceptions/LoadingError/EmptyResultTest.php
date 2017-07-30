<?php

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class EmptyResultTest extends TestCase {

    public function testGetMessage() {
      $e = new EmptyResult();
      $this->assertEquals(
        'Parsing result did not contain an usable node.',
        $e->getMessage()
      );
    }

  }
}
