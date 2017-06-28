<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class CommentTest extends TestCase {

    /**
     * @covers \FluentDOM\Comment
     */
    public function testMagicMethodToString() {
      $document = new Document();
      $document->appendElement('test')->appendChild($document->createComment('success'));
      $this->assertEquals(
        'success',
        (string)$document->documentElement->childNodes->item(0)
      );
      $this->assertEquals(
        '<test><!--success--></test>',
        $document->saveXML($document->documentElement)
      );
    }
  }
}