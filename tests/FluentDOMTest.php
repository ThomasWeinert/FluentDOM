<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

require_once __DIR__.'/FluentDOM/TestCase.php';

class FluentDOMTest extends \FluentDOM\TestCase  {

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testQuery(): void {
    $query = FluentDOM::Query();
    $this->assertInstanceOf(\FluentDOM\Query::class, $query);
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testQueryWithNode(): void {
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
  public function testCreator(): void {
    $write = FluentDOM::create();
    $this->assertInstanceOf(\FluentDOM\Creator::class, $write);
    $this->assertXmlStringEqualsXmlString(
      "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<test/>\n",
      (string)$write('test')
    );
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testCreatorWithArguments(): void {
    $write = FluentDOM::create('2.0', 'ASCII');
    $this->assertInstanceOf(\FluentDOM\Creator::class, $write);
    $this->assertEquals(
      "<?xml version=\"2.0\" encoding=\"ASCII\"?>\n<test/>\n",
      (string)$write('test')
    );
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testLoadWithDefaultLoader(): void {
    FluentDOM::setLoader(NULL);
    $document = FluentDOM::load('<foo/>');
    $this->assertXmlStringEqualsXmlString('<foo/>', $document->saveXml());
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testLoadWithDefinedLoader(): void {
    $result = new \FluentDOM\Loader\Result(
      $document = new \FluentDOM\DOM\Document(),
      'type'
    );
    $loader = $this->createMock(\FluentDOM\Loadable::class);
    $loader
      ->expects($this->once())
      ->method('load')
      ->with('source', 'type')
      ->willReturn($result);
    FluentDOM::setLoader($loader);
    $this->assertSame(
      $document,
      FluentDOM::load('source', 'type')
    );
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testSetLoaderWithInvalidObject(): void {
    $this->expectException(\FluentDOM\Exception::class);
    FluentDOM::setLoader(new stdClass());
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterLoader(): void {
    $document = new \FluentDOM\DOM\Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->method('supports')
      ->willReturn(TRUE);
    $mockLoader
      ->method('load')
      ->with('test.xml', 'mock/loader')
      ->willReturn(new \FluentDOM\Loader\Result($document, 'mock/loader'));
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
  public function testRegisterLoaderWithContentTypes(): void {
    $document = new \FluentDOM\DOM\Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->method('supports')
      ->willReturn(TRUE);
    $mockLoader
      ->method('load')
      ->with('test.xml', 'two')
      ->willReturn(new \FluentDOM\Loader\Result($document, ''));
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
  public function testRegisterLoaderWithCallable(): void {
    $document = new \FluentDOM\DOM\Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->method('supports')
      ->willReturn(TRUE);
    $mockLoader
      ->method('load')
      ->with('test.xml', 'some/type')
      ->willReturn(new \FluentDOM\Loader\Result($document, ''));
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
  public function testGetXPathTransformerAfterRegister(): void {
    $transformer = $this->getMockBuilder(FluentDOM\Xpath\Transformer::class)->getMock();
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
  public function testGetXPathTransformerAfterRegisterWithCallback(): void {
    $transformer = $this->getMockBuilder(\FluentDOM\Xpath\Transformer::class)->getMock();
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
  public function testGetXPathTransformerAfterRegisterwithClassName(): void {
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
  public function testGetXPathTransformerExpectingException(): void {
    FluentDOM::registerXpathTransformer('', TRUE);
    $this->expectException(\LogicException::class, 'No CSS selector support installed');
    FluentDOM::getXPathTransformer();
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterSerializerFactory(): void {
    $factory = function() {};
    FluentDOM::registerSerializerFactory($factory, 'example/type');
    $this->assertSame($factory, FluentDOM::getSerializerFactories()['example/type']);
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testGetSerializerFactories(): void {
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
  public function testSave(): void {
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
  public function testSaveWithChildNode(): void {
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
  public function testSaveWithQueryObject(): void {
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
  public function testSaveWithContentTypeHTML(): void {
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
  public function testSaveWithInvalidContentType(): void {
    $this->expectException(\FluentDOM\Exceptions\NoSerializer::class);
    $this->assertEquals(
      "<input>\n", FluentDOM::save(new \FluentDOM\DOM\Document(), 'type/invalid')
    );
  }
}

class FluentDOMXpathTransformer_TestProxy implements \FluentDOM\Xpath\Transformer {
  public function toXpath(
    string $selector,
    int $contextMode = FluentDOM\Xpath\Transformer::CONTEXT_CHILDREN,
    bool $isHtml = false):string {
    return '';
  }
}
