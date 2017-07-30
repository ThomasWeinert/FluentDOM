<?php

namespace FluentDOM\Query {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loader\Html;
  use FluentDOM\Loader\Result;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class Issue71Test extends TestCase {

    public function testNoLinefeedAfterTextNode() {
      $fd = FluentDOM('<div></div>', 'html-fragment');
      $fd->html(FluentDOM('hihi', 'html-fragment'));

      $this->assertEquals(
        "<div>hihi</div>\n",
        (string)$fd
      );
    }

    public function testLoaderAddsNoLineFeedToHtmlFragmentWithText() {
      $loader = new Html();
      /** @var Result $result */
      $result = $loader->load('hihi', 'html-fragment');
      $this->assertEquals(
        "hihi",
        (string)$result->getSelection()[0]
      );
    }

    public function testSaveHtmlDoesNotAddSpaceToTextOnlyDocument() {
      $document = new Document();
      $document->appendChild($document->createTextNode('hihi'));
      $this->assertEquals(
        "hihi",
        (string)$document->saveHtml()
      );
    }

    public function testSaveHtmlDoesNotAddSpaceToDocumentWithMultipleElementNodes() {
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

