<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class EmptySourceTest extends TestCase {

    public function testGetMessage(): void {
      $e = new EmptySource();
      $this->assertEquals(
        'Given source was empty.',
        $e->getMessage()
      );
    }

  }
}
