<?php

namespace FluentDOM\Serializer {

  use FluentDOM\DOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../TestCase.php');

  class HtmlTest extends TestCase  {

    public function testToString() {
      $document = new Document();
      $document->loadHTML(self::HTML);
      $serializer = new Html($document);
      $this->assertXmlStringEqualsXmlString(
        self::HTML, (string)$serializer
      );
    }
  }
}
