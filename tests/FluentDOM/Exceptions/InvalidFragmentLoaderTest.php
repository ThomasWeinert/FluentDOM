<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';


  class InvalidFragmentLoaderTest extends TestCase  {

    public function testConstructor(): void {
      $exception = new InvalidFragmentLoader('LoaderClass');
      $this->assertEquals(
        'Loader "LoaderClass" can not load fragments.', $exception->getMessage()
      );
    }
  }
}
