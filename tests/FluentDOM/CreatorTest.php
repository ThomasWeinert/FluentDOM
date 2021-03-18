<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;

  require_once __DIR__ . '/TestCase.php';

  /**
   * @covers \FluentDOM\Creator
   * @covers \FluentDOM\Creator\Node
   */
  class CreatorTest extends TestCase {


    public function testClone(): void {
      $creator = new Creator();
      $clone = clone $creator;
      $this->assertNotSame(
        $creator->document, $clone->document
      );
    }


    public function testCreate(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml/>',
        (string)$_('xml')
      );
    }

    public function testWithNamespace(): void {
      $_ = new Creator();
      $_->registerNamespace('#default', 'urn:foo');
      $this->assertXmlStringEqualsXmlString(
        '<xml xmlns="urn:foo"/>',
        (string)$_('xml')
      );
    }


    public function testCreateFetchingDocument(): void {
      $_ = new Creator();
      $document = $_('xml', $_('child'))->document;
      $this->assertInstanceOf(Document::class, $document);
      $this->assertXmlStringEqualsXmlString(
        '<xml><child/></xml>', $document->saveXml()
      );
    }


    public function testCreateIssetDocument(): void {
      $_ = new Creator();
      $node = $_('xml', $_('child'));
      $this->assertTrue(isset($node->document));
    }


    public function testCreateFetchingDom(): void {
      $_ = new Creator();
      $document = $_('xml', $_('child'))->document;
      $this->assertInstanceOf(Document::class, $document);
      $this->assertXmlStringEqualsXmlString(
        '<xml><child/></xml>', $document->saveXml()
      );
    }


    public function testCreateIssetNode(): void {
      $_ = new Creator();
      $node = $_('xml', $_('child'));
      $this->assertTrue(isset($node->node));
    }


    public function testCreateFetchingNode(): void {
      $_ = new Creator();
      $node = $_('xml', $_('child'))->node;
      $this->assertInstanceOf(Element::class, $node);
      $this->assertXmlStringEqualsXmlString(
        '<xml><child/></xml>', $node->saveXml()
      );
    }


    public function testElement(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml attr="value"/>',
        $_->element('xml', ['attr' => 'value'])->saveXml()
      );
    }


    public function testElementWithAttributes(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml attr="value"/>',
        $_->element('xml', ['attr' => 'value'])->saveXml()
      );
    }


    public function testCreateWithTextNode(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml>text</xml>',
        $_->element('xml', 'text')->saveXml()
      );
    }


    public function testCreateWithSeveralTextNodes(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml>onetwo</xml>',
        $_->element('xml', 'one', 'two')->saveXml()
      );
    }


    public function testCreateWithChildNodes(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><child-one/><child-two/></xml>',
        $_->element('xml', $_('child-one'), $_('child-two'))->saveXml()
      );
    }


    public function testCreateWithCdata(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><![CDATA[ cdata-text ]]></xml>',
        $_->element('xml', $_->cdata(' cdata-text '))->saveXml()
      );
    }


    public function testCreateWithComment(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><!--comment-text--></xml>',
        $_->element('xml', $_->comment('comment-text'))->saveXml()
      );
    }


    public function testCreateWithProcessingInstruction(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><?pi content?></xml>',
        $_->element('xml', $_->pi('pi', 'content'))->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Creator
     * @covers \FluentDOM\Creator\Nodes
     */
    public function testEach(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><one/><two/></xml>',
        $_->element(
          'xml',
          $_->each(
            [$_('one'), $_('two')]
          )
        )->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Creator
     * @covers \FluentDOM\Creator\Nodes
     */
    public function testEachWithIterator(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><one/><two/></xml>',
        $_->element(
          'xml',
          $_->each(
            new \ArrayIterator([$_('one'), $_('two')])
          )
        )->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Creator
     * @covers \FluentDOM\Creator\Nodes
     */
    public function testEachWithIteratorAggregate(): void {
      $_ = new Creator();
      $mock = $this->getMockBuilder(\IteratorAggregate::class)->getMock();
      $mock
        ->expects($this->once())
        ->method('getIterator')
        ->will(
          $this->returnValue(
            new \ArrayIterator([$_('one'), $_('two')])
          )
        );
      $this->assertXmlStringEqualsXmlString(
        '<xml><one/><two/></xml>',
        $_->element(
          'xml',
          $_->each($mock)
        )->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Creator
     * @covers \FluentDOM\Creator\Nodes
     */
    public function testEachWithMapping(): void {
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><one/><two/></xml>',
        $_->element(
          'xml',
          $_->each(
            ['one', 'two'],
            function ($name) use ($_) {
              return $_($name);
            }
          )
        )->saveXml()
      );
    }

    public function testEachReturnsIterator(): void {
      $_ = new Creator();
      $iterator = $_->each(['one', 'two']);
      $this->assertInstanceOf(
        Creator\Nodes::class, $iterator
      );
      $this->assertEquals(
        ['one', 'two'], iterator_to_array($iterator)
      );
    }

    public function testCreateWithDOMNode(): void {
      $document = new \DOMDocument();
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml><child/></xml>',
        $_->element('xml', $document->createElement('child'))->saveXml()
      );
    }


    public function testCreateWithAttributeNode(): void {
      $document = new \DOMDocument();
      $attribute = $document->createAttribute('attr');
      $attribute->value = 'value';
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml attr="value"/>',
        $_->element('xml', $attribute)->saveXml()
      );
    }


    public function testCreateWithAttributeAppendable(): void {
      $appendable = $this->getMockBuilder(Appendable::class)->getMock();
      $appendable
        ->expects($this->once())
        ->method('appendTo')
        ->with($this->isInstanceOf(Element::class));
      $document = new \DOMDocument();
      $attribute = $document->createAttribute('attr');
      $attribute->value = 'value';
      $_ = new Creator();
      $this->assertXmlStringEqualsXmlString(
        '<xml/>',
        $_->element('xml', $appendable)->saveXml()
      );
    }

    public function testCreateFetchingUnknownPropertyExpectingNull(): void {
      $_ = new Creator();
      $this->assertNull(
        $_('foo')->unknown
      );
    }

    public function testCreateIssetUnknownPropertyOnResultExpectingFalse(): void {
      $_ = new Creator();
      $this->assertFalse(isset($_('foo')->SOME_PROPERTY));
    }

    public function testCreateSetPropertyOnResultExpectingException(): void {
      $_ = new Creator();
      $this->expectException(\LogicException::class);
      $_('foo')->document = 'bar';
    }

    public function testCreateUnsetPropertyOnResultExpectingException(): void {
      $_ = new Creator();
      $this->expectException(\LogicException::class);
      unset($_('foo')->document);
    }


    public function testCreatorGetFormatOutputAfterSet(): void {
      $_ = new Creator();
      $_->formatOutput = TRUE;
      $this->assertTrue(isset($_->formatOutput));
      $this->assertTrue($_->formatOutput);
    }


    public function testCreatorGetOptimizeNamespacesAfterSet(): void {
      $_ = new Creator();
      $_->optimizeNamespaces = FALSE;
      $this->assertTrue(isset($_->optimizeNamespaces));
      $this->assertFalse($_->optimizeNamespaces);
    }


    public function testCreatorGetUnknownPropertyExpectingNull(): void {
      $_ = new Creator();
      $this->assertFalse(isset($_->unkown));
      $this->assertNull($_->unkown);
    }


    public function testCreatorGetUnknownPropertyAfterSet(): void {
      $_ = new Creator();
      $_->unkown = TRUE;
      $this->assertTrue($_->unkown);
    }


    public function testCreatorOptimizesNamespacesByDefault(): void {
      $_ = new Creator();
      $_->registerNamespace('foo', 'urn:foo');
      $document = $_(
        'root',
        $_('foo:child')
      )->document;
      $this->assertEquals('urn:foo', $document->documentElement->getAttribute('xmlns:foo'));
      $this->assertEquals('', $document->documentElement->firstChild->getAttribute('xmlns:foo'));
    }


    public function testCreatorOptimizeNamespacesCanBeDisabled(): void {
      $_ = new Creator();
      $_->registerNamespace('foo', 'urn:foo');
      $_->optimizeNamespaces = FALSE;
      $document = $_(
        'root',
        $_('foo:child')
      )->document;
      $this->assertEquals('urn:foo', $document->documentElement->getAttribute('xmlns:foo'));
      $this->assertEquals('urn:foo', $document->documentElement->firstChild->getAttribute('xmlns:foo'));
    }


    public function testResultIsTraversableOfNodes(): void {
      $_ = new Creator();
      $result = $_('foo');
      $this->assertInstanceOf(\Traversable::class, $result);
      $this->assertSame([$result->node], iterator_to_array($result));
    }
  }
}
