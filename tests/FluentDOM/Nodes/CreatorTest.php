<?php
namespace FluentDOM\Nodes {

  use FluentDOM\TestCase;
  use FluentDOM\Nodes;

  require_once(__DIR__.'/../TestCase.php');

  class CreatorTest extends TestCase {

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElement() {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml/>',
        (string)$_('xml')
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementFetchingDocument() {
      $_ = new Creator();
      $dom = $_('xml', $_('child'))->document;
      $this->assertInstanceOf('FluentDOM\Document', $dom);
      $this->assertXmlStringEqualsXmlString(
        '<xml><child/></xml>', $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementFetchingNode() {
      $_ = new Creator();
      $node = $_('xml', $_('child'))->node;
      $this->assertInstanceOf('FluentDOM\Element', $node);
      $this->assertXmlStringEqualsXmlString(
        '<xml><child/></xml>', $node->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementWithAttributes() {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml attr="value"/>',
        (string)$_('xml', ['attr' => 'value'])
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementWithTextNode() {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml>text</xml>',
        (string)$_('xml', 'text')
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementWithSeveralTextNodes() {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml>onetwo</xml>',
        (string)$_('xml', 'one', 'two')
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementWithChildNodes() {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><child-one/><child-two/></xml>',
        (string)$_('xml', $_('child-one'), $_('child-two'))
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementWithCdata() {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><![CDATA[ cdata-text ]]></xml>',
        (string)$_('xml', $_->cdata(' cdata-text '))
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementWithComment() {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><!--comment-text--></xml>',
        (string)$_('xml', $_->comment('comment-text'))
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementWithProcessingInstruction() {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><?pi content?></xml>',
        (string)$_('xml', $_->pi('pi', 'content'))
      );
    }

    /**
     * @covers FluentDOM\Nodes\Creator
     * @covers FluentDOM\Nodes\Creator\Node
     */
    public function testCreateElementWithDOMNode() {
      $dom = new \DOMDocument();
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><child/></xml>',
        (string)$_('xml', $dom->createElement('child'))
      );
    }
  }
}