<?php

require_once(__DIR__.'/../vendor/autoload.php');

class FluentDOMTest extends \PHPUnit_Framework_TestCase {

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
    $dom = new DOMDocument();
    $dom->appendChild($dom->createElement('test'));
    $query = FluentDOM::Query($dom->documentElement);
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
      ->will($this->returnValue(new FluentDOM\Document()));
    FluentDOM::setLoader($loader);
    $this->assertInstanceOf(FluentDOM\Document::class, FluentDOM::load('source', 'type'));
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM
   */
  public function testSetLoaderWithInvalidObject() {
    $this->setExpectedException(\FluentDOM\Exception::class);
    FluentDOM::setLoader(new stdClass());
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterLoader() {
    $dom = new \FluentDOM\Document();
    $dom->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(['mock/loader']));
    $mockLoader
      ->expects($this->any())
      ->method('load')
      ->with('test.xml', 'mock/loader')
      ->will($this->returnValue($dom));
    FluentDOM::registerLoader($mockLoader);
    $this->assertEquals(
      $dom,
      FluentDOM::load('test.xml', "mock/loader")
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterLoaderWithContentTypes() {
    $dom = new \FluentDOM\Document();
    $dom->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(['one', 'two']));
    $mockLoader
      ->expects($this->any())
      ->method('load')
      ->with('test.xml', 'two')
      ->will($this->returnValue($dom));
    FluentDOM::registerLoader($mockLoader, 'one', 'two');
    $this->assertEquals(
      $dom,
      FluentDOM::load('test.xml', "two")
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testRegisterLoaderWithCallable() {
    $dom = new \FluentDOM\Document();
    $dom->loadXML('<success/>');
    $mockLoader = $this->getMockBuilder(\FluentDOM\Loadable::class)->getMock();
    $mockLoader
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(['some/type']));
    $mockLoader
      ->expects($this->any())
      ->method('load')
      ->with('test.xml', 'some/type')
      ->will($this->returnValue($dom));
    FluentDOM::registerLoader(function() use ($mockLoader) { return $mockLoader; });
    $this->assertEquals(
      $dom,
      FluentDOM::load('test.xml', 'some/type')
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM
   */
  public function testGetXPathTransformerAfterRegister() {
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
  public function testGetXPathTransformerAfterRegisterWithCallback() {
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
    $this->setExpectedException(\LogicException::class, 'No CSS selector support installed');
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
    $document = new \FluentDOM\Document();
    $serializers = FluentDOM::getSerializerFactories();
    $this->assertInstanceOf(FluentDOM\Serializer\Xml::class, $serializers->createSerializer('xml', $document));
    $this->assertInstanceOf(FluentDOM\Serializer\Html::class, $serializers->createSerializer('html', $document));
    $this->assertInstanceOf(FluentDOM\Serializer\Json::class, $serializers->createSerializer('json', $document));
  }
}

class FluentDOMXpathTransformer_TestProxy implements \FluentDOM\Xpath\Transformer {
  public function toXpath($selector, $contextMode = FluentDOM\Xpath\Transformer::CONTEXT_CHILDREN, $isHtml = false) {
    return null;
  }
}