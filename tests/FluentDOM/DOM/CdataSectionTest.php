<?php

namespace FluentDOM\DOM {

  require_once(__DIR__ . '/../TestCase.php');

  use FluentDOM\TestCase;

  class CdataSectionTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\CdataSection
     */
    public function testMagicMethodToString() {
      $document = new Document();
      $document->appendElement('test')->appendChild($document->createCDATASection('success'));
      $this->assertEquals(
        'success',
        (string)$document->documentElement->childNodes->item(0)
      );
      $this->assertEquals(
        '<test><![CDATA[success]]></test>',
        $document->saveXML($document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Node\WholeText
     * @covers \FluentDOM\DOM\Text
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