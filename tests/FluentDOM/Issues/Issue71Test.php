<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loader\HtmlLoader;
  use FluentDOM\Loader\LoaderResult;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class Issue71Test extends TestCase {

    public function testNoLinefeedAfterTextNode(): void {
      $fd = FluentDOM('<div></div>', 'html-fragment');
      $fd->html(FluentDOM('ho ho ho', 'html-fragment'));

      $this->assertEquals(
        "<div>ho ho ho</div>\n",
        (string)$fd
      );
    }

    public function testLoaderAddsNoLineFeedToHtmlFragmentWithText(): void {
      $loader = new HtmlLoader();
      /** @var LoaderResult $result */
      $result = $loader->load('ho ho ho', 'html-fragment');
      $this->assertEquals(
        "ho ho ho",
        (string)$result->getSelection()[0]
      );
    }

    public function testSaveHtmlDoesNotAddSpaceToTextOnlyDocument(): void {
      $document = new Document();
      $document->appendChild($document->createTextNode('ho ho ho'));
      $this->assertEquals(
        "ho ho ho",
        $document->saveHTML()
      );
    }

    public function testSaveHtmlDoesNotAddSpaceToDocumentWithMultipleElementNodes(): void {
      $document = new Document();
      $document->appendChild($document->createElement('b'));
      $document->appendChild($document->createElement('i'));
      $this->assertEquals(
        "<b></b><i></i>",
        $document->saveHTML()
      );
    }
  }
}

