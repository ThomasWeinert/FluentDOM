<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class CdataSectionTest extends TestCase {

    /**
     * @covers FluentDOM\CdataSection
     */
    public function testMagicMethodToString() {
      $dom = new Document();
      $dom->appendElement('test')->appendChild($dom->createCDATASection('success'));
      $this->assertEquals(
        'success',
        (string)$dom->documentElement->childNodes->item(0)
      );
      $this->assertEquals(
        '<test><![CDATA[success]]></test>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}