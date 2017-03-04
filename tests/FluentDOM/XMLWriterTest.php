<?php
namespace FluentDOM {

  require_once(__DIR__ . '/TestCase.php');

  class XMLWriterTest extends TestCase {

    public function testWriteSomeHtmlWithoutNamespacs() {
      $_ = new XMLWriter();
      $_->openMemory();
      $_->setIndent(2);
      $_->startDocument();

      $_->startElement('html');
        $_->startElement('head');
          $_->writeElement('title', 'Example Title');
        $_->endElement();
      $_->endElement();

      $_->endDocument();

      $this->assertXmlStringEqualsXmlString(
        '<html>'.
        '  <head>'.
        '    <title>Example Title</title>'.
        '  </head>'.
        '</html>',
        $_->outputMemory()
      );
    }

    public function testWriteAtom() {
      $_ = new XMLWriter();
      $_->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
      $_->openMemory();
      $_->setIndent(2);
      $_->startDocument();

      $_->startElement('atom:feed');
        $_->writeElement('atom:title', 'Example Feed');
        $_->startElement('atom:link', 'Example Feed');
          $_->writeAttribute('href', 'http://example.org/');
        $_->endElement();
      $_->endElement();

      $_->endDocument();

      $this->assertXmlStringEqualsXmlString(
        '<atom:feed xmlns:atom="http://www.w3.org/2005/Atom">'.
        '  <atom:title>Example Feed</atom:title>'.
        '  <atom:link href="http://example.org/"/>'.
        '</atom:feed>',
        $_->outputMemory()
      );
    }
  }
}