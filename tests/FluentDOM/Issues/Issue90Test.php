<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\Exceptions\LoadingError\FileNotLoaded;
  use FluentDOM\Loader\Options as LoaderOptions;

  require_once __DIR__.'/../TestCase.php';

  class Issue90Test extends TestCase {

    public function testLoadingNonExistingXMLFile(): void {
      $this->expectException(FileNotLoaded::class);
      $this->expectExceptionMessage('Could not load file: ');
      \FluentDOM::load(
        __DIR__.'/TestData/NonExisting.xml',
        'xml',
        [ LoaderOptions::ALLOW_FILE => true ]
      );
    }
  }

}
