<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loader;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class LoaderResultTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\LoaderResult
     */
    public function testConstructor(): void {
      $document = new Document();
      $result = new Loader\LoaderResult($document, 'text/xml');
      $this->assertSame($document, $result->getDocument());
      $this->assertEquals('text/xml', $result->getContentType());
      $this->assertNull($result->getSelection());
    }

    /**
     * @covers \FluentDOM\Loader\LoaderResult
     */
    public function testConstructorWithSelection(): void {
      $document = new Document();
      $document->appendElement('dummy');
      $result = new Loader\LoaderResult($document, 'text/xml', $document->documentElement);
      $this->assertSame($document, $result->getDocument());
      $this->assertSame($document->documentElement, $result->getSelection());
      $this->assertEquals('text/xml', $result->getContentType());
    }
  }
}
