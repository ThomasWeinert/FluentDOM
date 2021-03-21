<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Nodes {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class ModifierTest extends TestCase {

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testConstructor(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $modifier = new Modifier($document->documentElement);
      $this->assertSame(
        $document->documentElement,
        $modifier->getNode()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testAppendChildren(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $modifier = new Modifier($document->documentElement);
      $modifier->appendChildren(
        [$document->createElement('one'), $document->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/></test>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testReplaceChildren(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $document->documentElement->appendChild($document->createElement('fail'));
      $modifier = new Modifier($document->documentElement);
      $modifier->replaceChildren(
        [$document->createElement('one'), $document->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/></test>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testInsertChildrenBefore(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $document->documentElement->appendChild($document->createElement('three'));
      $modifier = new Modifier($document->documentElement);
      $modifier->insertChildrenBefore(
        [$document->createElement('one'), $document->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/><three/></test>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testInsertChildrenBeforeIntoEmptyElement(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $modifier = new Modifier($document->documentElement);
      $modifier->insertChildrenBefore(
        [$document->createElement('one'), $document->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/></test>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testInsertNodesAfter(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $document->documentElement->appendChild($document->createElement('one'));
      $document->documentElement->appendChild($document->createElement('three'));
      $modifier = new Modifier($document->documentElement->firstChild);
      $modifier->insertNodesAfter(
        [$document->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/><three/></test>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testInsertNodesAfterLastChild(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $document->documentElement->appendChild($document->createElement('one'));
      $document->documentElement->appendChild($document->createElement('three'));
      $modifier = new Modifier($document->documentElement->lastChild);
      $modifier->insertNodesAfter(
        [$document->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><three/><two/></test>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testInsertNodesBefore(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $document->documentElement->appendChild($document->createElement('one'));
      $document->documentElement->appendChild($document->createElement('three'));
      $modifier = new Modifier($document->documentElement->lastChild);
      $modifier->insertNodesBefore(
        [$document->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><one/><two/><three/></test>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testInsertNodesBeforeFirstChild(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $document->documentElement->appendChild($document->createElement('one'));
      $document->documentElement->appendChild($document->createElement('three'));
      $modifier = new Modifier($document->documentElement->firstChild);
      $modifier->insertNodesBefore(
        [$document->createElement('two')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><two/><one/><three/></test>',
        $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Modifier
     */
    public function testReplaceNode(): void {
      $document = new \DOMDocument();
      $document->appendChild($document->createElement('test'));
      $document->documentElement->appendChild($document->createElement('one'));
      $document->documentElement->appendChild($document->createElement('three'));
      $modifier = new Modifier($document->documentElement->firstChild);
      $modifier->replaceNode(
        [$document->createElement('two'), $document->createTextNode('four')]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test><two/>four<three/></test>',
        $document->saveXml()
      );
    }
  }
}
