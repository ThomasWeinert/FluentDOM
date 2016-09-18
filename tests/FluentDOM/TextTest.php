<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class TextTest extends TestCase {

    /**
     * @covers \FluentDOM\Text
     */
    public function testMagicMethodToString() {
      $dom = new Document();
      $dom->appendElement('test')->appendChild($dom->createTextNode('success'));
      $this->assertEquals(
        'success',
        (string)$dom->documentElement->childNodes->item(0)
      );
      $this->assertEquals(
        '<test>success</test>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}