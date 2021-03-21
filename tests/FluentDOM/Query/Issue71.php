<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loader\Html;
  use FluentDOM\Loader\Result;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class Issue71Test extends TestCase {

    public function testNoLinefeedAfterTextNode(): void {
      $fd = FluentDOM('<div></div>', 'html-fragment');
      $fd->html(FluentDOM('hihi', 'html-fragment'));

      $this->assertEquals(
        "<div>hihi</div>\n",
        (string)$fd
      );
    }

    public function testLoaderAddsNoLineFeedToHtmlFragmentWithText(): void {
      $loader = new Html();
      /** @var Result $result */
      $result = $loader->load('hihi', 'html-fragment');
      $this->assertEquals(
        "hihi",
        (string)$result->getSelection()[0]
      );
    }

    public function testSaveHtmlDoesNotAddSpaceToTextOnlyDocument(): void {
      $document = new Document();
      $document->appendChild($document->createTextNode('hihi'));
      $this->assertEquals(
        "hihi",
        (string)$document->saveHtml()
      );
    }

    public function testSaveHtmlDoesNotAddSpaceToDocumentWithMultipleElementNodes(): void {
      $document = new Document();
      $document->appendChild($document->createElement('b'));
      $document->appendChild($document->createElement('i'));
      $this->assertEquals(
        "<b></b><i></i>",
        (string)$document->saveHtml()
      );
    }
  }
}

