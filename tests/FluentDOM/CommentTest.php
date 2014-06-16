<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class CommentTest extends TestCase {

    /**
     * @covers FluentDOM\Comment
     */
    public function testMagicMethodToString() {
      $dom = new Document();
      $dom->appendElement('test')->appendChild($dom->createComment('success'));
      $this->assertEquals(
        'success',
        (string)$dom->documentElement->childNodes->item(0)
      );
      $this->assertEquals(
        '<test><!--success--></test>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}