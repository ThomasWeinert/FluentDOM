<?php
namespace FluentDOM {

  require_once(__DIR__ . '/TestCase.php');

  class XMLWriterTest extends TestCase {

    /**
     * @covers \FluentDOM\XMLWriter
     */
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

    /**
     * @covers \FluentDOM\XMLWriter
     */
    public function testWriteAtom() {
      $_ = new XMLWriter();
      $_->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
      $_->openMemory();
      $_->setIndent(2);
      $_->startDocument();

      $_->startElement('atom:feed');
        $_->writeElement('atom:title', 'Example Feed');
        $_->startElement('atom:link');
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

    /**
     * @covers \FluentDOM\XMLWriter
     */
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

    /**
     * @covers \FluentDOM\XMLWriter
     */
    public function testWriteElementAddingNamespace() {
      $_ = new XMLWriter();
      $_->registerNamespace('example', 'http://example.org/xmlns/2002/document');
      $_->openMemory();
      $_->startDocument();
      $_->writeElement('example:document');
      $_->endDocument();

      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'.
        '<example:document xmlns:example="http://example.org/xmlns/2002/document"/>',
        $_->outputMemory()
      );
    }

    /**
     * @covers \FluentDOM\XMLWriter
     */
    public function testStartAttributeWithoutNamespace() {
      $_ = new XMLWriter();
      $_->registerNamespace('example', 'http://example.org/xmlns/2002/document');
      $_->openMemory();
      $_->startDocument();
      $_->startElement('document');
      $_->startAttribute('test');
      $_->text('success');
      $_->endAttribute();
      $_->endElement();
      $_->endDocument();

      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'.
        '<document test="success"/>',
        $_->outputMemory()
      );
    }

    /**
     * @covers \FluentDOM\XMLWriter
     */
    public function testStartAttributeAddingNamespace() {
      $_ = new XMLWriter();
      $_->registerNamespace('example', 'http://example.org/xmlns/2002/document');
      $_->openMemory();
      $_->startDocument();
      $_->startElement('document');
      $_->startAttribute('example:test');
      $_->text('success');
      $_->endAttribute();
      $_->endElement();
      $_->endDocument();

      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'.
        '<document xmlns:example="http://example.org/xmlns/2002/document" example:test="success"/>',
        $_->outputMemory()
      );
    }

    /**
     * @covers \FluentDOM\XMLWriter
     */
    public function testStartAttributeForAddedNamespace() {
      $_ = new XMLWriter();
      $_->registerNamespace('example', 'http://example.org/xmlns/2002/document');
      $_->openMemory();
      $_->startDocument();
      $_->startElement('example:document');
      $_->startAttribute('example:test');
      $_->text('success');
      $_->endAttribute();
      $_->endElement();
      $_->endDocument();

      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'.
        '<example:document xmlns:example="http://example.org/xmlns/2002/document" example:test="success"/>',
        $_->outputMemory()
      );
    }

    /**
     * @covers \FluentDOM\XMLWriter
     */
    public function testWritettributeNSAddingNamespace() {
      $_ = new XMLWriter();
      $_->registerNamespace('example', 'http://example.org/xmlns/2002/document');
      $_->openMemory();
      $_->startDocument();
      $_->startElement('document');
      $_->writeAttribute('example:test', 'success');
      $_->endElement();
      $_->endDocument();

      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>'.
        '<document xmlns:example="http://example.org/xmlns/2002/document" example:test="success"/>',
        $_->outputMemory()
      );
    }
  }
}