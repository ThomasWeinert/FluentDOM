<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM {

  use FluentDOM\TestCase;

  /**
   * @coversDefaultClass \FluentDOM\DOM\Implementation
   */
  class ImplementationTest extends TestCase {

    /**
     * @covers ::createDocument
     */
    public function testCreateDocumentWithoutArgumentReturnsFluentDOMDocument() {
      $implementation = new Implementation();
      $this->assertInstanceOf(Document::class, $implementation->createDocument());
    }

    /**
     * @covers ::createDocument
     */
    public function testCreateDocumentWithElement() {
      $implementation = new Implementation();
      $document = $implementation->createDocument(NULL, 'html');
      $this->assertXmlStringEqualsXmlString(
        '<html/>', $document
      );
      $this->assertNull($document->namespaces()['#default']);
    }

    /**
     * @covers ::createDocument
     */
    public function testCreateDocumentWithXhtmlDocType() {
      $implementation = new Implementation();
      $document = $implementation->createDocument(
        NULL,
        'html',
        $implementation->createDocumentType(
          'html',
          '-//W3C//DTD XHTML 1.0 Strict//EN',
          'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'
        )
      );
      $this->assertEquals(
        "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
        "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
        "<html xmlns=\"http://www.w3.org/1999/xhtml\"></html>\n",
        (string)$document
      );
    }

    /**
     * @covers ::createDocument
     */
    public function testCreateDocumentWithHtmlDocType() {
      $implementation = new Implementation();
      $document = $implementation->createDocument(
        NULL,
        'html',
        $implementation->createDocumentType(
          'html'
        )
      );
      $this->assertEquals(
        "<!DOCTYPE html>\n<html></html>\n",
        $document->saveHTML()
      );
    }

    /**
     * @covers ::createDocument
     */
    public function testCreateDocumentWithElementInNamespace() {
      $implementation = new Implementation();
      $document = $implementation->createDocument('https://www.w3.org/1999/xhtml/', 'html');
      $this->assertXmlStringEqualsXmlString(
        '<html xmlns="https://www.w3.org/1999/xhtml/"/>', $document
      );
      $this->assertEquals(
        'https://www.w3.org/1999/xhtml/', $document->namespaces()['#default']
      );
    }

    /**
     * @covers ::createDocument
     */
    public function testCreateDocumentWithElementInNamespacewithPrefix() {
      $implementation = new Implementation();
      $document = $implementation->createDocument('https://www.w3.org/1999/xhtml/', 'xhtml:html');
      $this->assertXmlStringEqualsXmlString(
        '<xhtml:html xmlns:xhtml="https://www.w3.org/1999/xhtml/"/>', $document
      );
      $this->assertEquals(
        'https://www.w3.org/1999/xhtml/', $document->namespaces()['xhtml']
      );
    }

    /**
     * @covers ::createDocument
     */
    public function testCreateAtomUsingAutomaticNamespaceRegistration() {
      $implementation = new Implementation();
      $document = $implementation->createDocument('http://www.w3.org/2005/Atom', 'atom:feed');
      $feed = $document->documentElement;
      $feed->appendElement('atom:title', 'Example Feed');
      $feed->appendElement('atom:link', ['href' => 'http://example.org/']);
      $this->assertXmlStringEqualsXmlString(
        '<atom:feed xmlns:atom="http://www.w3.org/2005/Atom">
          <atom:title>Example Feed</atom:title>
          <atom:link href="http://example.org/"/>
        </atom:feed>',
        $document
      );
    }
  }
}
