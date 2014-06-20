<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class NodesTest extends TestCase {

    public static $_fd;

    /**
     * @covers FluentDOM\Nodes::__construct
     */
    public function testConstructorWithXml() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('xml'));
      $fd = new Nodes($dom);
      $this->assertEquals(
        '<?xml version="1.0"?>'."\n".'<xml/>'."\n",
        (string)$fd
      );
    }

    /**
     * @covers FluentDOM\Nodes::__construct
     */
    public function testConstructorWithHtml() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('html'));
      $fd = new Nodes($dom, 'html');
      $this->assertEquals(
        '<html></html>'."\n",
        (string)$fd
      );
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::load
     */
    public function testLoadWithNodes() {
      $fdOne = new Nodes();
      $fdTwo = new Nodes();
      $fdTwo->load($fdOne);
      $this->assertSame($fdOne->document, $fdTwo->document);
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::load
     */
    public function testLoadWithDocument() {
      $fd = new Nodes();
      $fd->load($dom = new \DOMDocument());
      $this->assertSame(
        $dom,
        $fd->document
      );
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::load
     */
    public function testLoadWithDomNode() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('test'));
      $fd = new Nodes();
      $fd->load($dom->documentElement);
      $this->assertSame($dom, $fd->document);
      $this->assertCount(1, $fd);
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::load
     */
    public function testLoadWithCustomLoader() {
      $loader = $this->getMock('FluentDOM\Loadable');
      $loader
        ->expects($this->once())
        ->method('supports')
        ->with('text/xml')
        ->will($this->returnValue(TRUE));
      $loader
        ->expects($this->once())
        ->method('load')
        ->with('DATA', 'text/xml')
        ->will($this->returnValue($dom = new \DOMDocument()));
      $fd = new Nodes();
      $fd->loaders($loader);
      $fd->load('DATA');
      $this->assertSame(
        $dom,
        $fd->document
      );
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::load
     */
    public function testLoadWithInvalidArgumentExpectingException() {
      $fd = new Nodes();
      $this->setExpectedException('InvalidArgumentException');
      $fd->load(NULL, 'unknown');
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::load
     * @covers FluentDOM\Nodes::setContentType
     */
    public function testLoadWithInvalidContentTypeFallbackToXml() {
      $dom = new Document();
      $fd = new Nodes();
      $fd->load($dom, 'unknown');
      $this->assertEquals('text/xml', $fd->contentType);
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::loaders
     */
    public function testLoadersGetAfterSet() {
      $loader = $this->getMock('FluentDOM\Loadable');
      $fd = new Nodes();
      $fd->loaders($loader);
      $this->assertSame($loader, $fd->loaders());
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::loaders
     */
    public function testLoadersGetImplicitCreate() {
      $fd = new Nodes();
      $this->assertInstanceOf('FluentDOM\Loadable', $fd->loaders());
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::loaders
     */
    public function testLoadersGetCreateFromArray() {
      $fd = new Nodes();
      $fd->loaders([$loader = $this->getMock('FluentDOM\Loadable')]);
      $this->assertInstanceOf('FluentDOM\Loadable', $fd->loaders());
      $this->assertSame([$loader], iterator_to_array($fd->loaders()));
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::loaders
     */
    public function testLoadersGetWithInvalidLoaderExpectingException() {
      $fd = new Nodes();
      $this->setExpectedException('InvalidArgumentException');
      $fd->loaders('FOO');
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::push
     */
    public function testPushWithNode() {
      $fd = new Nodes();
      $fd->document->appendElement('test');
      $fd->push($fd->document->documentElement);
      $this->assertCount(1, $fd);
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::push
     */
    public function testPushWithTraversable() {
      $fd = new Nodes();
      $fd->document->appendElement('test');
      $fd->push([$fd->document->documentElement]);
      $this->assertCount(1, $fd);
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::push
     */
    public function testPushWithInvalidArgumentExpectingException() {
      $fd = new Nodes();
      $this->setExpectedException('InvalidArgumentException');
      $fd->push(FALSE);
    }

    /**
     * @group Load
     * @covers FluentDOM\Nodes::push
     */
    public function testPushNodeFromDifferentDocumentExpectingException() {
      $dom = new Document();
      $dom->appendElement('test');
      $fd = new Nodes();
      $this->setExpectedException('OutOfBoundsException');
      $fd->push($dom->documentElement);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::formatOutput
     */
    public function testFormatOutput() {
      $fd = new Nodes();
      $fd->document->loadXml('<html><body><br/></body></html>');
      $fd->formatOutput();
      $expected =
        "<?xml version=\"1.0\"?>\n".
        "<html>\n".
        "  <body>\n".
        "    <br/>\n".
        "  </body>\n".
        "</html>\n";
      $this->assertEquals('text/xml', $fd->contentType);
      $this->assertSame($expected, (string)$fd);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::formatOutput
     */
    public function testFormatOutputWithContentTypeHtml() {
      $fd = new Nodes();
      $fd->document->loadXml('<html><body><br/></body></html>');
      $fd->formatOutput('text/html');
      $expected = "<html><body><br></body></html>\n";
      $this->assertEquals('text/html', $fd->contentType);
      $this->assertSame($expected, (string)$fd);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::item
     */
    public function testItem() {
      $fd = new Nodes();
      $fd = $fd->find('/*');
      $this->assertEquals($fd->document->documentElement, $fd->item(0));
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::item
     */
    public function testItemExpectingNull() {
      $fd = new Nodes();
      $this->assertNull($fd->item(0));
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::spawn
     */
    public function testSpawn() {
      $fdParent = new Nodes;
      $fdChild = $fdParent->spawn();
      $this->assertAttributeSame(
        $fdParent,
        '_parent',
        $fdChild
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::spawn
     */
    public function testSpawnWithElements() {
      $dom = new \DOMDocument;
      $node = $dom->createElement('test');
      $dom->appendChild($node);
      $fdParent = new Nodes();
      $fdParent->load($dom);
      $fdChild = $fdParent->spawn($node);
      $this->assertSame(
        array($node),
        iterator_to_array($fdChild)
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::unique
     */
    public function testUnique() {
      $fd = new Nodes();
      $fd->document->appendElement('test');
      $nodes = $fd->unique(
        [$fd->document->documentElement, $fd->document->documentElement]
      );
      $this->assertCount(1, $nodes);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::unique
     */
    public function testUniqueWithUnattachedNodes() {
      $fd = new Nodes();
      $node = $fd->document->createElement("test");
      $nodes = $fd->unique([$node, $node]);
      $this->assertCount(1, $nodes);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::unique
     */
    public function testUniqueWithInvalidElementInList() {
      $fd = new Nodes();
      $this->setExpectedException('InvalidArgumentException');
      $fd->unique(['Invalid']);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::matches
     * @covers FluentDOM\Nodes::prepareSelector
     */
    public function testMatchesWithNodeListExpectingTrue() {
      $fd = new Nodes(self::XML);
      $this->assertTrue($fd->matches('/*'));
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::matches
     * @covers FluentDOM\Nodes::prepareSelector
     */
    public function testMatchesWithSelectorCallbackExpectingTrue() {
      $fd = new Nodes(self::XML);
      $fd->onPrepareSelector = function($selector) { return '/'.$selector; };
      $this->assertTrue($fd->matches('*'));
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::matches
     * @covers FluentDOM\Nodes::prepareSelector
     */
    public function testMatchesWithNodeListExpectingFalse() {
      $fd = new Nodes(self::XML);;
      $this->assertFalse($fd->matches('invalid'));
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::matches
     * @covers FluentDOM\Nodes::prepareSelector
     */
    public function testMatchesWithScalarExpectingTrue() {
      $fd = new Nodes(self::XML);;
      $this->assertTrue(
        $fd->matches('count(/items)')
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::matches
     * @covers FluentDOM\Nodes::prepareSelector
     */
    public function testMatchesWithScalarAndContextExpectingTrue() {
      $fd = new Nodes(self::XML);
      $this->assertTrue(
        $fd->matches(
          'count(item)',
          $fd->xpath()->evaluate('//group')->item(0)
        )
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::matches
     * @covers FluentDOM\Nodes::prepareSelector
     */
    public function testMatchesWithScalarExpectingFalse() {
      $fd = new Nodes(self::XML);
      $this->assertFalse(
        $fd->matches('count(item)')
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::matches
     * @covers FluentDOM\Nodes::prepareSelector
     */
    public function testMatchesWithPreparedSelectorExpectingTrue() {
      $fd = new Nodes(self::XML);
      $fd->onPrepareSelector = function($selector) {
        return 'count(//group[1]'.$selector.')';
      };
      $this->assertTrue(
        $fd->matches('/item')
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::matches
     * @covers FluentDOM\Nodes::prepareSelector
     */
    public function testMatchesWithPreparedSelectorExpectingFalse() {
      $fd = new Nodes(self::XML);
      $fd->onPrepareSelector = function($selector) {
        return 'count('.$selector.')';
      };
      $this->assertFalse(
        $fd->matches('/item')
      );
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::__get
     * @covers FluentDOM\Nodes::__set
     */
    public function testGetAfterSetOnPrepareSelector() {
      $fd = new Nodes();
      $fd->onPrepareSelector = $callback = function() {};
      $this->assertSame($callback, $fd->onPrepareSelector);
      $spawn = $fd->spawn();
      $this->assertSame($callback, $spawn->onPrepareSelector);
    }

    /**
     * @group CoreFunctions
     * @covers FluentDOM\Nodes::__set
     */
    public function testSetOnPrepareSelectorExpectingException() {
      $fd = new Nodes();
      $this->setExpectedException('InvalidArgumentException');
      $fd->onPrepareSelector = FALSE;
    }

    /**
     * @group Interfaces
     * @group IteratorAggregate
     * @covers FluentDOM\Nodes::getIterator
     */
    public function testIterator() {
      $fd = new Nodes(self::XML);
      $fd = $fd->find('//item');
      $this->assertInstanceOf('FluentDOM\Iterators\NodesIterator', $fd->getIterator());
      $this->assertcount(3, iterator_to_array($fd));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Nodes::offsetExists
     *
     */
    public function testOffsetExistsExpectingTrue() {
      $fd = new Nodes(self::XML);
      $fd = $fd->find('//item');
      $this->assertTrue(isset($fd[1]));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Nodes::offsetExists
     *
     */
    public function testOffsetExistsExpectingFalse() {
      $fd = new Nodes();
      $fd = $fd->find('//item');
      $this->assertFalse(isset($fd[99]));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Nodes::offsetGet
     */
    public function testOffsetGet() {
      $fd = new Nodes(self::XML);
      $fd = $fd->find('//item');
      $this->assertEquals('text2', $fd[1]->nodeValue);
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Nodes::offsetSet
     */
    public function testOffsetSetExpectingException() {
      $fd = new Nodes(self::XML);
      $fd = $fd->find('//item');
      $this->setExpectedException('BadMethodCallException');
      $fd[2] = '123';
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers FluentDOM\Nodes::offsetUnset
     */
    public function testOffsetUnsetExpectingException() {
      $fd = new Nodes();
      $fd = $fd->find('//item');
      $this->setExpectedException('BadMethodCallException');
      unset($fd[2]);
    }

    /**
     * @group Interfaces
     * @group Countable
     * @covers FluentDOM\Nodes::count
     */
    public function testInterfaceCountableExpecting3() {
      $fd = new Nodes(self::XML);
      $fd = $fd->find('//item');
      $this->assertCount(3, $fd);
    }

    /**
     * @group Interfaces
     * @group Countable
     * @covers FluentDOM\Nodes::count
     */
    public function testInterfaceCountableExpectingZero() {
      $fd = new Nodes(self::XML);
      $this->assertCount(0, $fd);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__isset
     * @covers FluentDOM\Nodes::__get
     * @covers FluentDOM\Nodes::__set
     * @covers FluentDOM\Nodes::__unset
     */
    public function testDynamicProperty() {
      $fd = new Nodes();
      $this->assertEquals(FALSE, isset($fd->dynamicProperty));
      $this->assertEquals(NULL, $fd->dynamicProperty);
      $fd->dynamicProperty = 'test';
      $this->assertEquals(TRUE, isset($fd->dynamicProperty));
      $this->assertEquals('test', $fd->dynamicProperty);
      unset($fd->dynamicProperty);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__unset
     */
    public function testDynamicPropertyUnsetOnNonExistingPropertyExpectingException() {
      $fd = new Nodes();
      $this->setExpectedException('BadMethodCallException');
      unset($fd->dynamicProperty);
    }

    /**
     * @covers FluentDOM\Nodes::__set
     */
    public function testSetPropertyXpath() {
      $fd = new Nodes(self::XML);;
      $this->setExpectedException('BadMethodCallException');
      $fd->xpath = $fd->xpath();
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__isset
     */
    public function testIssetPropertyLength() {
      $fd = new Nodes();
      $this->assertTrue(isset($fd->length));
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__get
     */
    public function testGetPropertyLength() {
      $fd = new Nodes(self::XML);
      $fd = $fd->find('//item');
      $this->assertEquals(3, $fd->length);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__set
     */
    public function testSetPropertyLength() {
      $fd = new Nodes();
      $this->setExpectedException('BadMethodCallException');
      $fd->length = 50;
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__unset
     */
    public function testUnsetPropertyLength() {
      $fd = new Nodes;
      $this->setExpectedException('BadMethodCallException');
      unset($fd->length);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__isset
     */
    public function testIssetPropertyDocumentExpectingFalse() {
      $fd = new Nodes();
      $this->assertFalse(isset($fd->document));
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__isset
     */
    public function testIssetPropertyDocumentExpectingTrue() {
      $fd = new Nodes();
      $fd->document;
      $this->assertTrue(isset($fd->document));
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__get
     * @covers FluentDOM\Nodes::getDocument
     */
    public function testGetPropertyDocumentImplicitCreate() {
      $fd = new Nodes;
      $document = $fd->document;
      $this->assertInstanceOf('FluentDOM\\Document', $document);
      $this->assertSame($document, $fd->document);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__isset
     */
    public function testIssetPropertyContentType() {
      $fd = new Nodes();
      $this->assertTrue(isset($fd->contentType));
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__get
     */
    public function testGetPropertyContentType() {
      $fd = new Nodes();
      $this->assertEquals('text/xml', $fd->contentType);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__set
     * @covers FluentDOM\Nodes::setContentType
     * @dataProvider getContentTypeSamples
     */
    public function testSetPropertyContentType($contentType, $expected) {
      $fd = new Nodes();
      $fd->contentType = $contentType;
      $this->assertAttributeEquals($expected, '_contentType', $fd);
    }

    public function getContentTypeSamples() {
      return array(
        array('text/xml', 'text/xml'),
        array('text/html', 'text/html'),
        array('xml', 'text/xml'),
        array('html', 'text/html'),
        array('TEXT/XML', 'text/xml'),
        array('TEXT/HTML', 'text/html'),
        array('XML', 'text/xml'),
        array('HTML', 'text/html')
      );
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__set
     * @covers FluentDOM\Nodes::setContentType
     */
    public function testSetPropertyContentTypeChaining() {
      $fdParent = new Nodes();
      $fdChild = $fdParent->spawn();
      $fdChild->contentType = 'text/html';
      $this->assertEquals(
        'text/html',
        $fdParent->contentType
      );
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__set
     * @covers FluentDOM\Nodes::setContentType
     */
    public function testSetPropertyContentTypeInvalid() {
      $fd = new Nodes();
      $this->setExpectedException('UnexpectedValueException');
      $fd->contentType = 'Invalid Type';
    }

    /**
     * @group Properties
     * @covers FluentDOM\Nodes::__get
     */
    public function testGetPropertyXpath() {
      $fd = new Nodes();
      $this->assertInstanceOf('FluentDOM\Xpath', $fd->xpath);
    }

    /**
     * @group MagicFunctions
     * @group StringCastable
     * @covers FluentDOM\Nodes::__toString
     */
    public function testMagicToString() {
      $fd = new Nodes(self::XML);;
      $this->assertEquals($fd->document->saveXML(), (string)$fd);
    }

    /**
     * @group MagicFunctions
     * @group StringCastable
     * @covers FluentDOM\Nodes::__toString
     */
    public function testMagicToStringHtml() {
      $dom = new \DOMDocument();
      $dom->loadHTML(self::HTML);
      $fd = new Nodes();
      $fd = $fd->load($dom);
      $fd->contentType = 'html';
      $this->assertEquals($dom->saveHTML(), (string)$fd);
    }



    /**
     * @group Core
     * @covers FluentDOM\Nodes::xpath()
     */
    public function testXpathGetFromDocument() {
      $dom = new Document();
      $fd = new Nodes();
      $fd = $fd->load($dom);
      $this->assertSame(
        $dom->xpath(), $fd->xpath()
      );
    }

    /**
     * @group Core
     * @covers FluentDOM\Nodes::xpath()
     */
    public function testXpathGetImplicitCreate() {
      $dom = new \DOMDocument();
      $fd = new Nodes();
      $fd = $fd->load($dom);
      $xpath = $fd->xpath();
      $this->assertSame(
        $xpath, $fd->xpath()
      );
    }

    /**
     * @group Core
     * @covers FluentDOM\Nodes::xpath()
     * @covers FluentDOM\Nodes::registerNamespace()
     * @covers FluentDOM\Nodes::applyNamespaces
     */
    public function testXpathGetImplicitCreateWithNamespace() {
      $dom = new \DOMDocument();
      $fd = new Nodes();
      $fd = $fd->load($dom);
      $fd->registerNamespace('foo', 'urn:foo');
      $xpath = $fd->xpath();
      $this->assertSame(
        $xpath, $fd->xpath()
      );
    }

    /**
     * @group Core
     * @covers FluentDOM\Nodes::registerNamespace
     * @covers FluentDOM\Nodes::applyNamespaces
     */
    public function testRegisterNamespaceBeforeLoad() {
      $fd = new Nodes();
      $fd->registerNamespace('f', 'urn:foo');
      $fd->load('<foo:foo xmlns:foo="urn:foo"/>');
      $this->assertEquals(1, $fd->xpath()->evaluate('count(/f:foo)'));
    }

    /**
     * @group Core
     * @covers FluentDOM\Nodes::registerNamespace
     * @covers FluentDOM\Nodes::applyNamespaces
     */
    public function testRegisterNamespaceAfterLoad() {
      $fd = new Nodes();
      $fd->load('<foo:foo xmlns:foo="urn:foo"/>', 'text/xml');
      $fd->registerNamespace('f', 'urn:foo');
      $this->assertEquals(1, $fd->xpath()->evaluate('count(/f:foo)'));
    }

    /**
     * @group Core
     * @covers FluentDOM\Nodes::registerNamespace
     * @covers FluentDOM\Nodes::applyNamespaces
     */
    public function testRegisterNamespaceAfterLoadOnCreatedXpath() {
      $dom = new \DOMDocument();
      $dom->loadXML('<foo:foo xmlns:foo="urn:foo"/>');
      $fd = new Nodes();
      $fd->load($dom, 'text/xml');
      $fd->xpath();
      $fd->registerNamespace('f', 'urn:foo');
      $this->assertEquals(1, $fd->xpath()->evaluate('count(/f:foo)'));
    }

    /**
     * @covers FluentDOM\Nodes::isNode
     */
    public function testIsNodeExpectingNode() {
      $fd = new Nodes(self::XML);
      $this->assertInstanceOf(
        'DOMNode', $fd->isNode($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Nodes::isNode
     */
    public function testIsNodeWithSelectorExpectingNode() {
      $fd = new Nodes(self::XML);
      $this->assertInstanceOf(
        'DOMNode',
        $fd->isNode($fd->document->documentElement, FALSE, 'name() = "items"')
      );
    }

    /**
     * @covers FluentDOM\Nodes::isNode
     */
    public function testIsNodeWithSelectorExpectingNull() {
      $fd = new Nodes(self::XML);
      $this->assertNull(
        $fd->isNode($fd->document->documentElement, FALSE, 'name() = "fail"')
      );
    }

    /**
     * @covers FluentDOM\Nodes::getSelectorCallback
     */
    public function testGetSelectorCallbackWithNullExpectingNull() {
      $fd = new Nodes(self::XML);
      $this->assertNull(
        $callback = $fd->getSelectorCallback(NULL)
      );
    }

    /**
     * @covers FluentDOM\Nodes::getSelectorCallback
     */
    public function testGetSelectorCallbackWithStringExpectingTrue() {
      $fd = new Nodes(self::XML);
      $this->assertInstanceOf(
        'Closure',
        $callback = $fd->getSelectorCallback('name() = "items"')
      );
      $this->assertTrue(
        $callback($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Nodes::getSelectorCallback
     */
    public function testGetSelectorCallbackWithNodeExpectingTrue() {
      $fd = new Nodes(self::XML);
      $this->assertInstanceOf(
        'Closure',
        $callback = $fd->getSelectorCallback($fd->document->documentElement)
      );
      $this->assertTrue(
        $callback($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Nodes::getSelectorCallback
     */
    public function testGetSelectorCallbackWithNodeArrayExpectingTrue() {
      $fd = new Nodes(self::XML);
      $this->assertInstanceOf(
        'Closure',
        $callback = $fd->getSelectorCallback(
          array($fd->document->documentElement)
        )
      );
      $this->assertTrue(
        $callback($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Nodes::getSelectorCallback
     */
    public function testGetSelectorCallbackWithCallableExpectingTrue() {
      $fd = new Nodes(self::XML);
      $this->assertInstanceOf(
        'Closure',
        $callback = $fd->getSelectorCallback(
          function (\DOMNode $node) {
            return $node instanceof \DOMElement;
          }
        )
      );
      $this->assertTrue(
        $callback($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Nodes::getSelectorCallback
     */
    public function testGetSelectorCallbackWithEmptyArrayExpectingFalse() {
      $fd = new Nodes(self::XML);
      $this->assertInstanceOf(
        'Closure',
        $callback = $fd->getSelectorCallback(
          array()
        )
      );
      $this->assertFalse(
        $callback($fd->document->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Nodes::getSelectorCallback
     */
    public function testGetSelectorCallbackWithInvalidSelectorExpectingException() {
      $fd = new Nodes(self::XML);
      $this->setExpectedException(
        'InvalidArgumentException',
        'Invalid selector argument.'
      );
      $fd->getSelectorCallback('');
    }

  }
}