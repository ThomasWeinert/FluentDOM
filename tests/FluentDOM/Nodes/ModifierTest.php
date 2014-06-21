<?php
namespace FluentDOM\Nodes {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class ModifierTest extends TestCase {

    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testConstructor() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $modifier = new Modifier($dom->documentElement);
      $this->assertSame(
        $dom->documentElement,
        $modifier->getNode()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testAppendChildren() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $modifier = new Modifier($dom->documentElement);
      $modifier->appendChildren(
        [$dom->createElement('one'), $dom->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/></test>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testReplaceChildren() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $dom->documentElement->appendChild($dom->createElement('fail'));
      $modifier = new Modifier($dom->documentElement);
      $modifier->replaceChildren(
        [$dom->createElement('one'), $dom->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/></test>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testInsertChildrenBefore() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $dom->documentElement->appendChild($dom->createElement('three'));
      $modifier = new Modifier($dom->documentElement);
      $modifier->insertChildrenBefore(
        [$dom->createElement('one'), $dom->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/><three/></test>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testInsertChildrenBeforeIntoEmptyElement() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $modifier = new Modifier($dom->documentElement);
      $modifier->insertChildrenBefore(
        [$dom->createElement('one'), $dom->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/></test>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testInsertNodesAfter() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $dom->documentElement->appendChild($dom->createElement('one'));
      $dom->documentElement->appendChild($dom->createElement('three'));
      $modifier = new Modifier($dom->documentElement->firstChild);
      $modifier->insertNodesAfter(
        [$dom->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/><three/></test>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testInsertNodesAfterLastChild() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $dom->documentElement->appendChild($dom->createElement('one'));
      $dom->documentElement->appendChild($dom->createElement('three'));
      $modifier = new Modifier($dom->documentElement->lastChild);
      $modifier->insertNodesAfter(
        [$dom->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><three/><two/></test>',
        $dom->saveXml()
      );
    }



    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testInsertNodesBefore() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $dom->documentElement->appendChild($dom->createElement('one'));
      $dom->documentElement->appendChild($dom->createElement('three'));
      $modifier = new Modifier($dom->documentElement->lastChild);
      $modifier->insertNodesBefore(
        [$dom->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/><three/></test>',
        $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Nodes\Modifier
     */
    public function testInsertNodesBeforeFirstChild() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $dom->documentElement->appendChild($dom->createElement('one'));
      $dom->documentElement->appendChild($dom->createElement('three'));
      $modifier = new Modifier($dom->documentElement->firstChild);
      $modifier->insertNodesBefore(
        [$dom->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><two/><one/><three/></test>',
        $dom->saveXml()
      );
    }
  }
}