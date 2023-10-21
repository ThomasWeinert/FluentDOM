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
   * @covers \FluentDOM\Exceptions\LoadingError\Libxml
   */
  class LibxmlTest extends TestCase {

    public function testWithErrorFromString(): void {
      $error = new \LibXMLError();
      $error->level = LIBXML_ERR_FATAL;
      $error->file = '';
      $error->line = 21;
      $error->column = 2;
      $error->message = 'message';
      $error->code = 42;
      $e = new Libxml($error);
      $this->assertEquals(
        'Libxml fatal error in line 21 at character 2: message.',
        $e->getMessage()
      );
    }

    public function testWithErrorFromFile(): void {
      $error = new \LibXMLError();
      $error->level = LIBXML_ERR_FATAL;
      $error->file = 'demo.xml';
      $error->line = 21;
      $error->column = 2;
      $error->message = 'message';
      $error->code = 42;
      $e = new Libxml($error);
      $this->assertEquals(
        'Libxml fatal error in demo.xml line 21 at character 2: message.',
        $e->getMessage()
      );
    }
  }
}
