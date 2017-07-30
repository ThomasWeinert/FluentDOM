<?php

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class EmptySourceTest extends TestCase {

    public function testGetMessage() {
      $e = new EmptySource();
      $this->assertEquals(
        'Given source was empty.',
        $e->getMessage()
      );
    }

  }
}
