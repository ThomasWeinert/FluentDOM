<?php

require_once(__DIR__.'/FluentDOM/TestCase.php');

class FluentDOMTest extends \FluentDOM\TestCase  {

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testQuery() {
    $query = FluentDOM::Query();
    $this->assertInstanceOf(\FluentDOM\Query::class, $query);
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testQueryWithNode() {
    $document = new DOMDocument();
    $document->appendChild($document->createElement('test'));
    $query = FluentDOM::Query($document->documentElement);
    $this->assertCount(1, $query);
    $this->assertXmlStringEqualsXmlString("<?xml version=\"1.0\"?>\n<test/>\n", (string)$query);
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testCreator() {
    $write = FluentDOM::create();
    $this->assertInstanceOf(\FluentDOM\Nodes\Creator::class, $write);
    $this->assertXmlStringEqualsXmlString(
      "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<test/>\n",
      (string)$write('test')
    );
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testCreatorWithArguments() {
    $write = FluentDOM::create('2.0', 'ASCII');
    $this->assertInstanceOf(\FluentDOM\Nodes\Creator::class, $write);
    $this->assertEquals(
      "<?xml version=\"2.0\" encoding=\"ASCII\"?>\n<test/>\n",
      (string)$write('test')
    );
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testLoadWithDefaultLoader() {
    FluentDOM::setLoader(NULL);
    $document = FluentDOM::load('<foo/>');
    $this->assertXmlStringEqualsXmlString('<foo/>', $document->saveXml());
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testLoadWithDefinedLoader() {
    $loader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $loader
      ->expects($this->once())
      ->method('load')
      ->with('source', 'type')
      ->will($this->returnValue(new FluentDOM\DOM\Document()));
    FluentDOM::setLoader($loader);
    $this->assertInstanceOf(FluentDOM\DOM\Document::class, FluentDOM::load('source', 'type'));
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testSetLoaderWithInvalidObject() {
    $this->expectException(\FluentDOM\Exception::class);
    FluentDOM::setLoader(new stdClass());
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterLoader() {
    $document = new \FluentDOM\DOM\Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(TRUE));
    $mockLoader
      ->expects($this->any())
      ->method('load')
      ->with('test.xml', 'mock/loader')
      ->will($this->returnValue($document));
    FluentDOM::registerLoader($mockLoader);
    $this->assertEquals(
      $document,
      FluentDOM::load('test.xml', "mock/loader")
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterLoaderWithContentTypes() {
    $document = new \FluentDOM\DOM\Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(TRUE));
    $mockLoader
      ->expects($this->any())
      ->method('load')
      ->with('test.xml', 'two')
      ->will($this->returnValue($document));
    FluentDOM::registerLoader($mockLoader, 'one', 'two');
    $this->assertEquals(
      $document,
      FluentDOM::load('test.xml', "two")
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterLoaderWithCallable() {
    $document = new \FluentDOM\DOM\Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(TRUE));
    $mockLoader
      ->expects($this->any())
      ->method('load')
      ->with('test.xml', 'some/type')
      ->will($this->returnValue($document));
    FluentDOM::registerLoader(function() use ($mockLoader) { return $mockLoader; });
    $this->assertEquals(
      $document,
      FluentDOM::load('test.xml', 'some/type')
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testGetXPathTransformerAfterRegister() {
    $transformer = $this->getMockBuilder(FluentDOM\DOM\Xpath\Transformer::class)->getMock();
    FluentDOM::registerXpathTransformer($transformer, TRUE);
    $this->assertSame(
      $transformer,
      FluentDOM::getXPathTransformer()
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testGetXPathTransformerAfterRegisterWithCallback() {
    $transformer = $this->getMockBuilder(\FluentDOM\DOM\Xpath\Transformer::class)->getMock();
    FluentDOM::registerXpathTransformer(
      function() use ($transformer) {
        return $transformer;
      },
      TRUE
    );
    $this->assertSame(
      $transformer,
      FluentDOM::getXPathTransformer()
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testGetXPathTransformerAfterRegisterwithClassName() {
    FluentDOM::registerXpathTransformer(
      FluentDOMXpathTransformer_TestProxy::class,
      TRUE
    );
    $this->assertInstanceOf(
      FluentDOMXpathTransformer_TestProxy::class,
      FluentDOM::getXPathTransformer()
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testGetXPathTransformerExpectingException() {
    FluentDOM::registerXpathTransformer('', TRUE);
    $this->expectException(\LogicException::class, 'No CSS selector support installed');
    FluentDOM::getXPathTransformer();
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterSerializerFactory() {
    $factory = function() {};
    FluentDOM::registerSerializerFactory($factory, 'example/type');
    $this->assertSame($factory, FluentDOM::getSerializerFactories()['example/type']);
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testGetSerializerFactories() {
    $document = new \FluentDOM\DOM\Document();
    $serializers = FluentDOM::getSerializerFactories();
    $this->assertInstanceOf(FluentDOM\Serializer\Xml::class, $serializers->createSerializer('xml', $document));
    $this->assertInstanceOf(FluentDOM\Serializer\Html::class, $serializers->createSerializer('html', $document));
    $this->assertInstanceOf(FluentDOM\Serializer\Json::class, $serializers->createSerializer('json', $document));
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testSave() {
    $document = new \FluentDOM\DOM\Document();
    $document->appendElement('foo');
    $this->assertXmlStringEqualsXmlString(
      '<foo/>', FluentDOM::save($document)
    );
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testSaveWithChildNode() {
    $document = new \FluentDOM\DOM\Document();
    $document->appendElement('foo')->appendElement('bar');
    $this->assertXmlStringEqualsXmlString(
      '<bar/>', FluentDOM::save($document->documentElement->firstChild)
    );
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testSaveWithQueryObject() {
    $document = new \FluentDOM\DOM\Document();
    $document->appendElement('foo');
    $this->assertXmlStringEqualsXmlString(
      '<foo/>', FluentDOM::save(FluentDOM($document->documentElement))
    );
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testSaveWithContentTypeHTML() {
    $document = new \FluentDOM\DOM\Document();
    $document->appendElement('input');
    $this->assertEquals(
      "<input>\n", FluentDOM::save($document, 'text/html')
    );
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testSaveWithInvalidContentType() {
    $this->expectException(\FluentDOM\Exceptions\NoSerializer::class);
    $this->assertEquals(
      "<input>\n", FluentDOM::save(new \FluentDOM\DOM\Document(), 'type/invalid')
    );
  }
}

class FluentDOMXpathTransformer_TestProxy implements \FluentDOM\DOM\Xpath\Transformer {
  public function toXpath(
    string $selector,
    int $contextMode = FluentDOM\DOM\Xpath\Transformer::CONTEXT_CHILDREN,
    bool $isHtml = false):string {
    return '';
  }
}