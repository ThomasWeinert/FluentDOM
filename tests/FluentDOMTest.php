<?php

require_once(__DIR__.'/../vendor/autoload.php');

class FluentDOMTest extends \PHPUnit_Framework_TestCase {

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::Query
   */
  public function testQuery() {
    $query = FluentDOM::Query();
    $this->assertInstanceOf('FluentDOM\Query', $query);
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::Query
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
   * @covers FluentDOM::create
   */
  public function testCreator() {
    $write = FluentDOM::create();
    $this->assertInstanceOf('FluentDOM\Nodes\Creator', $write);
    $this->assertXmlStringEqualsXmlString(
      "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<test/>\n",
      (string)$write('test')
    );
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::create
   */
  public function testCreatorWithArguments() {
    $write = FluentDOM::create('2.0', 'ASCII');
    $this->assertInstanceOf('FluentDOM\Nodes\Creator', $write);
    $this->assertEquals(
      "<?xml version=\"2.0\" encoding=\"ASCII\"?>\n<test/>\n",
      (string)$write('test')
    );
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::setLoader
   * @covers FluentDOM::load
   */
  public function testLoadWithDefaultLoader() {
    FluentDOM::setLoader(NULL);
    $document = FluentDOM::load('<foo/>');
    $this->assertXmlStringEqualsXmlString('<foo/>', $document->saveXml());
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::setLoader
   * @covers FluentDOM::load
   */
  public function testLoadWithDefinedLoader() {
    $loader = $this->getMock('FluentDOM\Loadable');
    $loader
      ->expects($this->once())
      ->method('load')
      ->with('source', 'type')
      ->will($this->returnValue(new FluentDOM\Document()));
    FluentDOM::setLoader($loader);
    $this->assertInstanceOf('FluentDOM\Document', FluentDOM::load('source', 'type'));
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::setLoader
   */
  public function testSetLoaderWithInvalidObject() {
    $this->setExpectedException('FluentDOM\Exception');
    FluentDOM::setLoader(new stdClass());
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM::registerLoader
   * @covers FluentDOM::getDefaultLoaders
   */
  public function testRegisterLoader() {
    $dom = new \FluentDOM\Document();
    $dom->loadXML('<success/>');
    $mockLoader = $this->getMock('FluentDOM\\Loadable');
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
   * @covers FluentDOM::registerXPathTransformer
   * @covers FluentDOM::getXPathTransformer
   */
  public function testGetXPathTransformerAfterRegister() {
    $transformer = $this->getMock('FluentDOM\\Xpath\\Transformer');
    FluentDOM::registerXpathTransformer($transformer, TRUE);
    $this->assertSame(
      $transformer,
      FluentDOM::getXPathTransformer()
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM::registerXPathTransformer
   * @covers FluentDOM::getXPathTransformer
   */
  public function testGetXPathTransformerAfterRegisterWithCallback() {
    $transformer = $this->getMock('FluentDOM\\Xpath\\Transformer');
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
   * @covers FluentDOM::registerXPathTransformer
   * @covers FluentDOM::getXPathTransformer
   */
  public function testGetXPathTransformerAfterRegisterwithClassName() {
    FluentDOM::registerXpathTransformer(
      'FluentDOMXpathTransformer_TestProxy',
      TRUE
    );
    $this->assertInstanceOf(
      'FluentDOMXpathTransformer_TestProxy',
      FluentDOM::getXPathTransformer()
    );
  }

  /**
   * @group FactoryFunctions
   * @group Plugins
   * @covers FluentDOM::registerXPathTransformer
   * @covers FluentDOM::getXPathTransformer
   */
  public function testGetXPathTransformerExpectingException() {
    FluentDOM::registerXpathTransformer('', TRUE);
    $this->setExpectedException('LogicException', 'No CSS selector support installed');
    FluentDOM::getXPathTransformer();
  }
}

class FluentDOMXpathTransformer_TestProxy implements \FluentDOM\Xpath\Transformer {
  public function toXpath($selector, $isDocumentContext = false, $isHtml = false) {
    return null;
  }
}