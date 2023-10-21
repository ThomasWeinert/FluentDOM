<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  /**
   * @covers \FluentDOM\Exceptions\SourceNotLoaded
   */
  class SourceNotLoadedTest extends TestCase {

    public function testGetMessage(): void {
      $e = new SourceNotLoaded();
      $this->assertEquals(
        'Could not load source.',
        $e->getMessage()
      );
    }

  }
}
