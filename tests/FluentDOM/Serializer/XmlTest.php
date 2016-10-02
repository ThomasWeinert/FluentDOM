<?php

namespace FluentDOM\Serializer {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../TestCase.php');

  class XmlTest extends TestCase  {

    public function testToString() {
      $document = new Document();
      $document->loadXML(self::XML);
      $serializer = new Xml($document);
      $this->assertXmlStringEqualsXmlString(
        self::XML, (string)$serializer
      );
    }
  }
}
