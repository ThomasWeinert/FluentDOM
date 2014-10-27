<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class NonDocumentTypeChildNodeTest extends TestCase {

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     */
    public function testNextElementSibling() {
      $dom = new Document();
      $dom->loadXML('<foo><bar index="0"/>TEXT<bar index="1"/></foo>');
      $node = $dom->documentElement->firstChild->nextElementSibling;
      $this->assertEquals(
        '<bar index="1"/>',
        $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Node\NonDocumentTypeChildNode\Implementation
     */
    public function testPreviousElementSibling() {
      $dom = new Document();
      $dom->loadXML('<foo><bar index="0"/>TEXT<bar index="1"/></foo>');
      $node = $dom->documentElement->lastChild->previousElementSibling;
      $this->assertEquals(
        '<bar index="0"/>',
        $node->saveXml()
      );
    }
  }
}