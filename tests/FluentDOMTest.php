<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

use FluentDOM\DOM\Document;
use FluentDOM\Exceptions\NoSerializer;
use FluentDOM\Loadable;
use FluentDOM\Loader\LoaderResult as LoaderResult;
use FluentDOM\TestCase;
use FluentDOM\Xpath\Transformer as XpathTransformer;

require_once __DIR__.'/FluentDOM/TestCase.php';

class FluentDOMTest extends TestCase  {

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
  public function testQueryCssWithNode(): void {
    FluentDOM::registerXpathTransformer(
      fn() => $this->createMock(XpathTransformer::class)
    );
    $document = new DOMDocument();
    $document->appendChild($document->createElement('test'));
    $query = FluentDOM::QueryCss($document->documentElement);
    $this->assertCount(1, $query);
    $this->assertXmlStringEqualsXmlString("<?xml version=\"1.0\"?>\n<test/>\n", (string)$query);
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testCreator(): void {
    $write = FluentDOM::create();
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
    $result = new LoaderResult(
      $document = new Document(),
      'type'
    );
    $loader = $this->createMock(Loadable::class);
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
    $this->expectException(TypeError::class);
    /** @noinspection PhpParamsInspection */
    FluentDOM::setLoader(new stdClass());
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterLoader(): void {
    $document = new Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->createMock(Loadable::class);
    $mockLoader
      ->method('supports')
      ->willReturn(TRUE);
    $mockLoader
      ->method('load')
      ->with('test.xml', 'mock/loader')
      ->willReturn(new LoaderResult($document, 'mock/loader'));
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
    $document = new Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->createMock(Loadable::class);
    $mockLoader
      ->method('supports')
      ->willReturn(TRUE);
    $mockLoader
      ->method('load')
      ->with('test.xml', 'two')
      ->willReturn(new LoaderResult($document, ''));
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
    $document = new Document();
    $document->loadXML('<success/>');
    $mockLoader = $this->createMock(Loadable::class);
    $mockLoader
      ->method('supports')
      ->willReturn(TRUE);
    $mockLoader
      ->method('load')
      ->with('test.xml', 'some/type')
      ->willReturn(new LoaderResult($document, ''));
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
    $transformer = $this->createMock(FluentDOM\Xpath\Transformer::class);
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
    $transformer = $this->createMock(XpathTransformer::class);
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
  public function testGetXPathTransformerAfterRegisterWithClassName(): void {
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
    $this->expectException(\LogicException::class);
    $this->expectExceptionMessage('No CSS selector support installed');
    FluentDOM::getXPathTransformer();
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterSerializerFactory(): void {
    $factory = static function() {};
    FluentDOM::registerSerializerFactory($factory, 'example/type');
    $this->assertSame($factory, FluentDOM::getSerializerFactories()['example/type']);
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testGetSerializerFactories(): void {
    $document = new Document();
    $serializers = FluentDOM::getSerializerFactories();
    $this->assertInstanceOf(FluentDOM\Serializer\XmlSerializer::class, $serializers->createSerializer($document, 'xml'));
    $this->assertInstanceOf(FluentDOM\Serializer\HtmlSerializer::class, $serializers->createSerializer($document, 'html'));
    $this->assertInstanceOf(FluentDOM\Serializer\JsonSerializer::class, $serializers->createSerializer($document, 'json'));
  }

  /**
   * @group FactoryFunction
   * @group Plugins
   * @covers FluentDOM
   */
  public function testSave(): void {
    $document = new Document();
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
    $document = new Document();
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
    $document = new Document();
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
    $document = new Document();
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
    $this->expectException(NoSerializer::class);
    $this->assertEquals(
      "<input>\n", FluentDOM::save(new Document(), 'type/invalid')
    );
  }
}

class FluentDOMXpathTransformer_TestProxy implements XpathTransformer {
  public function toXpath(
    string $selector,
    int $contextMode = FluentDOM\Xpath\Transformer::CONTEXT_CHILDREN,
    bool $isHtml = false):string {
    return '';
  }
}
