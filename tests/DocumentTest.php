<?php

namespace FluentDOM {

  require_once(__DIR__.'/../src/_require.php');

  class DocumentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers FluentDOM\Document::xpath
     */
    public function testXpathImplicitCreate() {
      $dom = new Document();
      $xpath = $dom->xpath();
      $this->assertInstanceOf(__NAMESPACE__.'\\Xpath', $xpath);
      $this->assertSame($xpath, $dom->xpath());
    }

    /**
     * @covers FluentDOM\Document::xpath
     */
    public function testXpathImplicitCreateAfterDocumentLoad() {
      $dom = new Document();
      $xpath = $dom->xpath();
      $dom->loadXML('<test/>');
      $this->assertInstanceOf(__NAMESPACE__.'\\Xpath', $xpath);
      $this->assertNotSame($xpath, $dom->xpath());
    }

    /**
     * @covers FluentDOM\Document::registerNamespace
     * @covers FluentDOM\Document::getNamespace
     */
    public function testGetNamespaceAfterRegister() {
      $dom = new Document();
      $dom->registerNamespace('test', 'urn:success');
      $this->assertEquals(
        'urn:success',
        $dom->getNamespace('test')
      );
    }

    /**
     * @covers FluentDOM\Document::getNamespace
     */
    public function testGetNamespaceWithoutRegisterExpectingNull() {
      $dom = new Document();
      $this->assertNull(
        $dom->getNamespace('test')
      );
    }

    /**
     * @covers FluentDOM\Document::createElement
     */
    public function testCreateElementWithNamespace() {
      $dom = new Document();
      $dom->registerNamespace('test', 'urn:success');
      $dom->appendChild($dom->createElement('test:example'));
      $this->assertEquals(
        '<test:example xmlns:test="urn:success"/>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Document::appendElement
     */
    public function testAppendElement() {
      $dom = new Document();
      $dom->appendElement('test', 'text', array('attribute' => 'value'));
      $this->assertEquals(
        '<test attribute="value">text</test>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}
