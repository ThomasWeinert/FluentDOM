<?php
namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class LibxmlTest extends TestCase {

    /**
     * @covers FluentDOM\Exceptions\LoadingError\Libxml
     */
    public function testWithErrorFromString() {
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

    /**
     * @covers FluentDOM\Exceptions\LoadingError\Libxml
     */
    public function testWithErrorFromFile() {
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