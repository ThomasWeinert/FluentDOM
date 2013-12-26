<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class ElementTest extends TestCase {

    /**
     * @covers FluentDOM\Element::appendElement
     */
    public function testAppendElement() {
      $dom = new Document();
      $dom->appendChild($dom->createElement('root'));
      $dom->documentElement->appendElement('test', 'text', array('attribute' => 'value'));
      $this->assertEquals(
        '<root><test attribute="value">text</test></root>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}