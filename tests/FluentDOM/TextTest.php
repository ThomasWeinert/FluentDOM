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

    /**
     * @covers \FluentDOM\Node\WholeText
     * @covers \FluentDOM\Text
     */
    public function testReplaceWholeText() {
      $document = new Document();
      $document->loadXML(
        '<p>Thru-hiking is great!  <strong>No insipid election coverage!</strong>'.
        ' However, <a href="http://en.wikipedia.org/wiki/Absentee_ballot">casting a'.
        ' ballot</a> is tricky.</p>'
      );
      $paragraph = $document->documentElement;
      $paragraph->removeChild($paragraph->childNodes->item(1));
      /** @var Text $text */
      $text = $paragraph->firstChild;
      $this->assertSame(
        $text, $text->replaceWholeText('Thru-hiking is great, but ')
      );
      $this->assertXmlStringEqualsXmlString(
        '<p>Thru-hiking is great, but <a'.
        ' href="http://en.wikipedia.org/wiki/Absentee_ballot">casting a'.
        ' ballot</a> is tricky.</p>',
        $paragraph->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Node\WholeText
     * @covers \FluentDOM\Text
     */
    public function testReplaceWholeTextWithEmptyString() {
      $document = new Document();
      $document->loadXML(
        '<!DOCTYPE p ['."\n".
        '  <!ENTITY ent "foo">'."\n".
        ']>'."\n".
        '<p>bar&ent;</p>'
      );
      /** @var CdataSection $text */
      $text = $document->documentElement->firstChild;
      $this->assertNull($text->replaceWholeText(''));
      $this->assertXmlStringEqualsXmlString(
        '<p></p>',
        $document->documentElement->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Node\WholeText
     * @covers \FluentDOM\Text
     */
    public function testReplaceWholeTextWithEntityReferenceExpectingException() {
      $document = new Document();
      $document->loadXML(
        '<!DOCTYPE p ['."\n".
        '  <!ENTITY ent "foo<br/>">'."\n".
        ']>'."\n".
        '<p>bar&ent;</p>'
      );
      /** @var CdataSection $text */
      $text = $document->documentElement->firstChild;
      $this->setExpectedException(\DOMException::class);
      $text->replaceWholeText('42');
    }

    /**
     * @covers \FluentDOM\Node\WholeText
     * @covers \FluentDOM\Text
     */
    public function testReplaceWholeTextWithEntityReferenceRecursion() {
      $document = new Document();
      $document->loadXML(
        '<!DOCTYPE p ['."\n".
        '  <!ENTITY one "&two;">'."\n".
        '  <!ENTITY two "21">'."\n".
        ']>'."\n".
        '<p>bar&one;</p>'
      );
      /** @var CdataSection $text */
      $text = $document->documentElement->firstChild;
      $text->replaceWholeText('42');
      $this->assertXmlStringEqualsXmlString(
        '<p>42</p>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Node\WholeText
     * @covers \FluentDOM\Text
     */
    public function testReplaceWholeTextRemovesEntity() {
      $document = new Document();
      $document->loadXML(
        '<!DOCTYPE p ['."\n".
        '  <!ENTITY t "world">'."\n".
        ']>'."\n".
        '<p>Hello &t;<br/>, nice to see you &t;.</p>'
      );
      /** @var \FluentDOM\Text $text */
      $text = $document->documentElement->firstChild;
      $text->replaceWholeText('Hi universe');
      $this->assertEquals(
        '<?xml version="1.0"?>'."\n".
        '<!DOCTYPE p ['."\n".
        '<!ENTITY t "world">'."\n".
        ']>'."\n".
        '<p>Hi universe<br/>, nice to see you &t;.</p>'."\n",
        $document->saveXML()
      );
    }
  }
}