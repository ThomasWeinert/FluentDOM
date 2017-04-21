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

    public function testWriteXmlWithAttributesInNamespace() {
      $_ = new XMLWriter();
      $_->registerNamespace('', 'http://example.org/xmlns/2002/document');
      $_->registerNamespace('xlink', 'http://www.w3.org/1999/xlink');
      $_->openMemory();
      $_->startDocument();

      $_->startElement('document');
        $_->applyNamespaces();
        $_->startElement('heading');
          $_->writeAttribute('id', 'someHeading');
          $_->text('Some Document');
        $_->endElement();
        $_->startElement('para');
          $_->text('Here is ');
          $_->startElement('anchor');
            $_->writeAttribute('xlink:href', '#someHeading');
            $_->writeAttribute('xlink:type', 'simple');
            $_->text('a link');
          $_->endElement();
          $_->text(' to the header.');
        $_->endElement();
      $_->endElement();

      $_->endDocument();

      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'.
        '<document xmlns="http://example.org/xmlns/2002/document" xmlns:xlink="http://www.w3.org/1999/xlink">'.
        '<heading id="someHeading">Some Document</heading>'.
        '<para>Here is <anchor xlink:type="simple" xlink:href="#someHeading">a link</anchor>'.
        ' to the header.</para>'.
        '</document>',
        $_->outputMemory()
      );
    }
  }
}