<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class CdataSectionTest extends TestCase {

    /**
     * @covers \FluentDOM\CdataSection
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

    /**
     * @covers \FluentDOM\Node\WholeText
     * @covers \FluentDOM\Text
     */
    public function testReplaceWholeText() {
      $document = new Document();
      $document->loadXML(
        '<p>'.
        'one<!-- start -->'.
        '<![CDATA[two]]>'.
        'three'.
        '<![CDATA[four]]>'.
        'five'.
        '<![CDATA[six]]><!-- end -->'.
        'seven'.
        '</p>'
      );
      /** @var CdataSection $textFour */
      $textFour = $document->documentElement->childNodes->item(4);
      $textFour->replaceWholeText('42');
      $this->assertEquals(
        '<p>one<!-- start --><![CDATA[42]]><!-- end -->seven</p>',
        $document->documentElement->saveXml()
      );
    }
  }
}