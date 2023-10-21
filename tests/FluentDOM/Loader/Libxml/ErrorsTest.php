<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\Libxml {

  use FluentDOM\DOM\Document;
  use FluentDOM\Exceptions\LoadingError\SourceNotLoaded;
  use FluentDOM\Loader\Libxml\Errors;
  use FluentDOM\Loader\Options;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  /**
   * @covers \FluentDOM\Loader\Libxml\Errors
   */
  class ErrorsTest extends TestCase {

    public function testInvalidReturnExpectingException() {
      $errors = new Errors();
      $this->expectException(SourceNotLoaded::class);
      $errors->capture(
        static function() {
          return NULL;
        }
      );
    }
  }
}
