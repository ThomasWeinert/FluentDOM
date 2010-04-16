<?php
/**
* Collection of tests for the FluentDOMCore class
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage unitTests
*/

/**
* load necessary files
*/
require_once (dirname(__FILE__).'/../FluentDOMTestCase.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOM.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMCoreTest extends PHPUnit_Framework_TestCase {

  const XML = '
    <items version="1.0">
      <group id="1st">
        <item index="0">text1</item>
        <item index="1">text2</item>
        <item index="2">text3</item>
      </group>
      <html>
        <div class="test1 test2">class testing</div>
        <div class="test2">class testing</div>
      </html>
    </items>
  ';

  /**
  * @group Load
  * @covers FluentDOMCore::load
  */
  public function testLoadWithInvalidSource() {
    $fd = new FluentDOMCore();
    try {
      $fd->load(1);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group Load
  * @covers FluentDOMCore::load
  */
  public function testLoadWithFluentDOM() {
    $fdParent = new FluentDOMCore();
    $fdChild = new FluentDOMCore();
    $fdChild->load($fdParent);
    $this->assertAttributeEquals(
      $fdParent,
      '_parent',
      $fdChild
    );
  }

  /**
  * @group Load
  * @covers FluentDOMCore::load
  * @covers FluentDOMCore::setLoaders
  */
  public function testLoaderMechanism() {
    $firstLoaderMock = $this->getMock('FluentDOMLoader');
    $firstLoaderMock
      ->expects($this->once())
      ->method('load')
      ->with($this->equalTo('test load string'), $this->equalTo('text/xml'))
      ->will($this->returnValue(FALSE));
    $secondLoaderMock = $this->getMock('FluentDOMLoader');
    $secondLoaderMock
      ->expects($this->once())
      ->method('load')
      ->with($this->equalTo('test load string'), $this->equalTo('text/xml'))
      ->will($this->returnValue(new DOMDocument()));

    $fd = new FluentDOMCore();
    $fd->setLoaders(array($firstLoaderMock, $secondLoaderMock));

    $this->assertSame(
      $fd,
      $fd->load('test load string')
    );
  }

  /**
  * @group Load
  * @covers FluentDOMCore::load
  */
  public function testLoaderMechanismIncludingSelection() {
    $dom = new DOMDocument();
    $domNode = $dom->appendChild($dom->createElement('root'));
    $loaderMock = $this->getMock('FluentDOMLoader');
    $loaderMock
      ->expects($this->once())
      ->method('load')
      ->with($this->equalTo($domNode), $this->equalTo('text/xml'))
      ->will($this->returnValue(array($dom, array($domNode))));

    $fd = new FluentDOMCore();
    $fd->setLoaders(array($loaderMock));

    $this->assertSame(
      $fd,
      $fd->load($domNode)
    );
  }

  /**
  * @group Load
  * @covers FluentDOMCore::load
  * @covers FluentDOMCore::_initLoaders
  */
  public function testLoadersMechanismDefaultLoaders() {
    $dom = new DOMDocument();
    $fd = new FluentDOMCoreProxy();
    $fd->load($dom);
    $this->assertAttributeNotEquals(
      array(),
      '_loaders',
      $fd
    );
  }

  /**
  * @group Load
  * @covers FluentDOMCore::setLoaders
  */
  public function testSetLoadersInvalid() {
    try {
      $fd = new FluentDOMCore();
      $fd->setLoaders(array(new stdClass));
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group Properties
  * @covers FluentDomCore::__isset
  */
  public function testIssetPropertyDocument() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertTrue(isset($fd->document));
  }

  /**
  * @group Properties
  * @covers FluentDomCore::__get
  */
  public function testGetPropertyDocument() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertSame(
      $this->readAttribute($fd, '_document'),
      $fd->document
    );
  }

  /**
  * @group Properties
  * @covers FluentDomCore::__set
  */
  public function testSetPropertyDocument() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    try {
      $fd->document = NULL;
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__isset
  */
  public function testIssetPropertyXpath() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertTrue(isset($fd->xpath));
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__get
  * @covers FluentDOMCore::_xpath
  */
  public function testGetPropertyXpath() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertTrue($fd->xpath instanceof DOMXPath);
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__get
  * @covers FluentDOMCore::_xpath
  */
  public function testGetPropertyXpathWithDefaultNamespaceInitialization() {
    $fd = $this->getFluentDOMCoreFixtureFromString('<sample xmlns="http://sample.tld/"/>');
    $this->assertEquals(
      1,
      $fd->xpath->evaluate('count(//_:*)')
    );
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__get
  * @covers FluentDOMCore::_xpath
  */
  public function testGetPropertyXpathWithNamespaceInitialization() {
    $fd = new FluentDOM();
    $fd->namespaces(
      array(
        'foo' => 'http://sample.tld/1',
        'bar' => 'http://sample.tld/2'
      )
    );
    $fd->document->loadXML(
      '<sample xmlns="http://sample.tld/1"><foo:child xmlns:foo="http://sample.tld/2"/></sample>'
    );
    $this->assertEquals(
      1,
      $fd->xpath->evaluate('count(//bar:child)')
    );
  }

  /**
  * @covers FluentDOMCore::__set
  */
  public function testSetPropertyXpath() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    try {
      $fd->xpath = NULL;
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__isset
  */
  public function testIssetPropertyLength() {
    $fd = new FluentDOMCore();
    $this->assertTrue(isset($fd->length));
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__get
  */
  public function testGetPropertyLength() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $this->assertEquals(3, $fd->length);
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__set
  */
  public function testSetPropertyLength() {
    $fd = new FluentDOMCore;
    try {
      $fd->length = 50;
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__isset
  */
  public function testIssetPropertyContentType() {
    $fd = new FluentDOMCore();
    $this->assertTrue(isset($fd->contentType));
  }


  /**
  * @group Properties
  * @covers FluentDOMCore::__get
  */
  public function testGetPropertyContentType() {
    $fd = new FluentDOMCore();
    $this->assertEquals('text/xml', $fd->contentType);
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__set
  * @covers FluentDOMCore::_setContentType
  * @dataProvider getContentTypeSamples
  */
  public function testSetPropertyContentType($contentType, $expected) {
    $fd = new FluentDOMCore();
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
  * @covers FluentDOMCore::__set
  * @covers FluentDOMCore::_setContentType
  */
  public function testSetPropertyContentTypeChaining() {
    $fdParent = new FluentDOMCore();
    $fdChild = $fdParent->spawn();
    $fdChild->contentType = 'text/html';
    $this->assertEquals(
      'text/html',
      $fdParent->contentType
    );
  }

  /**
  * @group Properties
  * @covers FluentDOMCore::__set
  * @covers FluentDOMCore::_setContentType
  */
  public function testSetPropertyContentTypeInvalid() {
    $fd = new FluentDOMCore();
    try {
      $fd->contentType = 'INVALID/NOT_USEABLE';
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  * @group  Properties
  * @covers FluentDOMCore::__set
  */
  public function testSetInvalidPropertyContentType() {
    try {
      $fd = new FluentDOMCore();
      $fd->contentType = 'INVALID_TYPE';
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  * @group Properties
  */
  public function testDynamicProperty() {
    $fd = new FluentDOMCore();
    $this->assertEquals(FALSE, isset($fd->dynamicProperty));
    $this->assertEquals(NULL, $fd->dynamicProperty);
    $fd->dynamicProperty = 'test';
    $this->assertEquals(TRUE, isset($fd->dynamicProperty));
    $this->assertEquals('test', $fd->dynamicProperty);
  }

  /*
  * __toString() method
  */

  /**
  * @group MagicFunctions
  */
  public function testMagicToString() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertEquals($fd->document->saveXML(), (string)$fd);
  }

  /**
  * @group MagicFunctions
  */
  public function testMagicToStringHtml() {
    $dom = new DOMDocument();
    $dom->loadHTML('<html><body><br></body></html>');
    $loader = $this->getMock('FluentDOMLoader');
    $loader
      ->expects($this->once())
      ->method('load')
      ->with($this->equalTo(''), $this->equalTo('text/html'))
      ->will($this->returnValue($dom));
    $fd = new FluentDOMCore();
    $fd->setLoaders(array($loader));
    $fd = $fd->load('', 'text/html');
    $this->assertEquals($dom->saveHTML(), (string)$fd);
  }

  /*
  * Interfaces
  */

  /**
  * @group Interfaces
  * @covers FluentDOMCore::offsetExists
  */
  public function testInterfaceArrayAccessIssetExpectingTrue() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '/*');
    $this->assertTrue(isset($fd[0]));
  }

  /**
  * @group Interfaces
  * @covers FluentDOMCore::offsetExists
  */
  public function testInterfaceArrayAccessIssetExpectingFalse() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '/*');
    $this->assertFalse(isset($fd[99]));
  }

  /**
  * @group Interfaces
  * @covers FluentDOMCore::offsetGet
  */
  public function testInterfaceArrayAccessGet() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $this->assertEquals(
      'item',
      $fd[0]->nodeName
    );
  }

  /**
  * @group Interfaces
  * @covers FluentDOMCore::offsetSet
  */
  public function testInterfaceArrayAccessSet() {
    $fd = new FluentDOMCore();
    try {
      $fd[0] = NULL;
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  * @group Interfaces
  * @covers FluentDOMCore::offsetUnset
  */
  public function testInterfaceArrayAccessUnset() {
    $fd = new FluentDOMCore();
    try {
      unset($fd[0]);
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  * @group Interfaces
  * @covers FluentDOMCore::count
  */
  public function testInterfaceCountableExpectingZero() {
    $fd = new FluentDOMCore();
    $this->assertEquals(0, count($fd));
  }

  /**
  * @group Interfaces
  * @covers FluentDOMCore::count
  */
  public function testInterfaceCountable() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $this->assertEquals(3, count($fd));
  }

  /**
  * @group Interfaces
  * @covers FluentDOMCore::getIterator
  */
  public function testInterfaceIteratorLoop() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $counter = 0;
    foreach ($fd as $item) {
      $this->assertEquals('item', $item->nodeName);
      ++$counter;
    }
    $this->assertEquals(3, $counter);
  }

  /**
  * @group Interfaces
  * @covers FluentDOMCore::getIterator
  */
  public function testInterfaceRecursiveIterator() {
    $iterator = new RecursiveIteratorIterator(
      $this->getFluentDOMCoreFixtureFromString(self::XML, '/items'),
      RecursiveIteratorIterator::SELF_FIRST
    );
    $counter = 0;
    foreach ($iterator as $key => $value) {
      if ($value->nodeName == 'item') {
        ++$counter;
      }
    }
    $this->assertEquals(3, $counter);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::item
  */
  public function testItem() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '/*');
    $this->assertEquals($fd->document->documentElement, $fd->item(0));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::item
  */
  public function testItemExpectingNull() {
    $fd = new FluentDOMCore();
    $this->assertNull($fd->item(0));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::formatOutput
  */
  public function testFormatOutput() {
    $fd = new FluentDOMCore();
    $fd->load('<html><body><br/></body></html>');
    $fd->formatOutput();
    $expected =
       "<?xml version=\"1.0\"?>\n".
       "<html>\n".
       "  <body>\n".
       "    <br/>\n".
       "  </body>\n".
       "</html>\n";
    $this->assertSame('text/xml', $this->readAttribute($fd, '_contentType'));
    $this->assertSame($expected, (string)$fd);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::formatOutput
  */
  public function testFormatOutputWithContentTypeHtml() {
    $fd = new FluentDOMCore();
    $fd->load('<html><body><br/></body></html>');
    $fd->formatOutput('text/html');
    $expected = "<html><body><br></body></html>\n";
    $this->assertSame('text/html', $this->readAttribute($fd, '_contentType'));
    $this->assertSame($expected, (string)$fd);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::spawn
  */
  public function testSpawn() {
    $fdParent = new FluentDOMCore;
    $fdChild = $fdParent->spawn();
    $this->assertAttributeSame(
      $fdParent,
      '_parent',
      $fdChild
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::push
  */
  public function testPushWithDomnode() {
    $fd = new FluentDOMCore();
    $node = $fd->document->createElement('sample');
    $fd->push($node);
    $this->assertAttributeSame(
      array($node),
      '_array',
      $fd
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::push
  */
  public function testPushWithArrayContainingDomnode() {
    $fd = new FluentDOMCore();
    $node = $fd->document->createElement('sample');
    $fd->push(array($node));
    $this->assertAttributeSame(
      array($node),
      '_array',
      $fd
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::push
  */
  public function testPushWithForeignDomnodeExpectingException() {
    $fd = new FluentDOMCore();
    $dom = new DOMDocument();
    $node = $dom->createElement('sample');
    try {
      $fd->push($node);
      $this->fail('An expected exception has not been raised.');
    } catch (OutOfBoundsException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::push
  */
  public function testPushWithArrayContainingForeignDomnodeExpectingException() {
    $fd = new FluentDOMCore();
    $dom = new DOMDocument();
    $node = $dom->createElement('sample');
    try {
      $fd->push(array($node));
      $this->fail('An expected exception has not been raised.');
    } catch (OutOfBoundsException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::push
  */
  public function testPushWithInvalidArgumentExpectingException() {
    $fd = new FluentDOMCore();
    try {
      $fd->push(42);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::unique
  */
  public function testUniqueWithArrayOfAppendedNodes() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $nodes = array(
      $fd[1],
      $fd[2],
      $fd[0],
      $fd[2],
      $fd[1],
      $fd[2],
      $fd[0]
    );
    $unique = $fd->unique($nodes);
    $this->assertEquals(3, count($unique));
    $this->assertSame($fd[0], $unique[0]);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::unique
  */
  public function testUniqueWithArrayOfCreatedNodes() {
    $fd = new FluentDOM();
    $nodes = array(
      $fd->document->createElement('hello'),
      $fd->document->createElement('world'),
      $fd->document->createTextNode('!')
    );
    $nodes[] = $nodes[2];
    $nodes[] = $nodes[1];
    $nodes[] = $nodes[0];
    $unique = $fd->unique($nodes);
    $this->assertEquals(3, count($unique));
    $this->assertEquals('hello', $unique[0]->tagName);
    $this->assertEquals('world', $unique[1]->tagName);
    $this->assertEquals('!', $unique[2]->textContent);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::unique
  */
  public function testUniqueWithArrayOfAppenedAndCreatedNodes() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $nodes = array(
      $fd->document->createElement('hello'),
      $fd[1],
      $fd->document->createTextNode('world'),
      $fd[0]
    );
    $unique = $fd->unique($nodes);
    $this->assertEquals(4, count($unique));
    $this->assertSame($fd[0], $unique[0]);
    $this->assertSame($fd[1], $unique[1]);
    $this->assertEquals('hello', $unique[2]->tagName);
    $this->assertEquals('world', $unique[3]->textContent);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::unique
  */
  public function testUniqueWithIntegerExpectingException() {
    $fd = new FluentDOM();
    try {
      $fd->unique(array(1));
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
      $this->assertEquals(
        'Array must only contain dom nodes, found "integer".',
        $expected->getMessage()
      );
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::unique
  */
  public function testUniqueWithObjectExpectingException() {
    $fd = new FluentDOM();
    try {
      $fd->unique(array(new stdClass));
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
      $this->assertEquals(
        'Array must only contain dom nodes, found "stdClass".',
        $expected->getMessage()
      );
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_uniqueSort
  */
  public function testUniqueSort() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $nodes = array(
      $fd->document->createElement('hello'),
      $fd[1],
      $fd->document->createTextNode('world'),
      $fd[0]
    );
    $fd->push($nodes);
    $fd->_uniqueSort();
    $this->assertEquals(5, count($fd));
    $this->assertSame($nodes[3], $fd[0]);
    $this->assertSame($nodes[1], $fd[1]);
    $this->assertSame($nodes[0], $fd[3]);
    $this->assertSame($nodes[2], $fd[4]);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::evaluate
  */
  public function testEvaluate() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertEquals(
      3,
      $fd->evaluate('count(//item)')
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::evaluate
  */
  public function testEvaluateWithContext() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertEquals(
      3,
      $fd->evaluate('count(group/item)', $fd->document->documentElement)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::namespaces
  */
  public function testNamespacesRegisterNamespaces() {
    $fd = new FluentDOM();
    $fdResult = $fd->namespaces(array('test' => 'http://test.only/'));
    $this->assertAttributeEquals(
      array('test' => 'http://test.only/'),
      '_namespaces',
      $fdResult
    );
    $this->assertSame($fd, $fdResult);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::namespaces
  */
  public function testNamespacesGetNamespaces() {
    $fd = new FluentDOM();
    $fd->namespaces(array('test' => 'http://test.only/'));
    $this->assertEquals(
      array('test' => 'http://test.only/'),
      $fd->namespaces()
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::namespaces
  */
  public function testNamespacesWithChaining() {
    $fd = new FluentDOM();
    $fd->namespaces(array('test' => 'http://test.only/'));
    $fdChild = $fd->spawn();
    $this->assertAttributeEquals(
      array('test' => 'http://test.only/'),
      '_namespaces',
      $fdChild
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_match
  */
  public function testMatch() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertEquals(
      3,
      $fd->_match('//item')->length
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_match
  */
  public function testMatchWithContext() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '/items/group');
    $this->assertEquals(
      3,
      $fd->_match('item', $fd->item(0))->length
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_test
  */
  public function testTestMatchingNodelist() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertTrue(
      $fd->_test('//item')
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_test
  */
  public function testTestCountingNodesWithContextExpectingTrue() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '/items/group');
    $this->assertTrue(
      $fd->_test('count(item)', $fd->item(0))
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_inList
  */
  public function testInListExpectingTrue() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '/items');
    $this->assertTrue(
      $fd->_inList($fd->document->documentElement)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_inList
  */
  public function testInListExpectingFalse() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $this->assertFalse(
      $fd->_inList($fd->document->documentElement)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_isQName
  * @dataProvider dataProviderValidQualifiedNames
  */
  public function testIsQName($qualifiedName) {
    $fd = new FluentDOMCoreProxy();
    $this->assertTrue($fd->_isQName($qualifiedName));
  }

  public static function dataProviderValidQualifiedNames() {
    return array(
      array('tag'),
      array('namespace:tag'),
      array('_:_'),
      array('_-_'),
      array('_')
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_isQName
  */
  public function testIsQnameWithEmptyNameExpectingException() {
    $fd = new FluentDOMCoreProxy();
    try {
      $fd->_isQName('');
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isNCName
  * @dataProvider dataProviderValidNCName
  */
  public function testIsNCName($tagName, $offset, $length) {
    $fd = new FluentDOMCoreProxy();
    $this->assertTrue($fd->_isNCName($tagName, $offset, $length));
  }

  public static function dataProviderValidNCName() {
    return array(
      array('html', 0, 0),
      array('tag23', 0, 0),
      array('sample-tag', 0, 0),
      array('sampleTag', 0, 0),
      array('ns:tag', 3, 0),
      array('ns:tag', 0, 2)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isNCName
  */
  public function testIsNCNameWithEmptyTagnameExpectingException() {
    $fd = new FluentDOMCoreProxy();
    try {
      $fd->_isNCName('nc:', 3);
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
      $this->assertEquals(
        'Invalid QName "nc:": Missing QName part.',
        $expected->getMessage()
      );
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isNCName
  */
  public function testIsNCNameWithInvalidTagnameCharExpectingException() {
    $fd = new FluentDOMCoreProxy();
    try {
      $fd->_isNCName('nc:ta<g>', 3);
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
      $this->assertEquals(
        'Invalid QName "nc:ta<g>": Invalid character at index 5.',
        $expected->getMessage()
      );
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isNCName
  */
  public function testIsNCNameWithInvalidTagnameStartingCharExpectingException() {
    $fd = new FluentDOMCoreProxy();
    try {
      $fd->_isNCName('nc:1tag', 3);
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
      $this->assertEquals(
        'Invalid QName "nc:1tag": Invalid character at index 3.',
        $expected->getMessage()
      );
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_isNode
  */
  public function testIsNodeWithDomnodeExpectingTrue() {
    $dom = new DOMDocument();
    $node = $dom->createElement('sample');
    $fd = new FluentDOMCoreProxy();
    $this->assertTrue($fd->_isNode($node));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_isNode
  */
  public function testIsNodeWithDomtextExpectingTrue() {
    $dom = new DOMDocument();
    $node = $dom->createTextNode('sample');
    $fd = new FluentDOMCoreProxy();
    $this->assertTrue($fd->_isNode($node));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMcore::_isNode
  */
  public function testIsNodeWithEmptyDomtextExpectingTrue() {
    $dom = new DOMDocument();
    $node = $dom->createTextNode('   ');
    $fd = new FluentDOMCoreProxy();
    $this->assertFalse($fd->_isNode($node));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isNodeList
  */
  public function testIsNodeListWithArrayExpectingTrue() {
    $fd = new FluentDOMCoreProxy();
    $this->assertTrue($fd->_isNodeList(array()));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isNodeList
  */
  public function testIsNodeListWithArrayExpectingFalse() {
    $fd = new FluentDOMCoreProxy();
    $this->assertFalse($fd->_isNodeList(42));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isCallback
  */
  public function testIsCallbackWithArrayCallbackExpectingTrue() {
    $fd = new FluentDOMCoreProxy();
    $this->assertTrue(
      $fd->_isCallback(array($this, __METHOD__), FALSE, FALSE)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isCallback
  */
  public function testIsCallbackWithGlobalFunctionExpectingTrue() {
    $fd = new FluentDOMCoreProxy();
    $this->assertTrue(
      $fd->_isCallback('strpos', TRUE, FALSE)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isCallback
  */
  public function testIsCallbackWithGlobalFunctionExpectingFalse() {
    $fd = new FluentDOMCoreProxy();
    $this->assertFalse(
      $fd->_isCallback('strpos', FALSE, TRUE)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isCallback
  */
  public function testIsCallbackWithInvalidCallbackExpectingException() {
    $fd = new FluentDOMCoreProxy();
    try {
      $fd->_isCallback('foo', FALSE, FALSE);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_isCallback
  */
  public function testIsCallbackWithInvalidCallbackExpectingFalse() {
    $fd = new FluentDOMCoreProxy();
    $this->assertFalse(
      $fd->_isCallback('foo', FALSE, TRUE)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentFragment
  */
  public function testGetContentFragmentIncludeTextNodes() {
    $fragment = '<sample/>sample';
    $fd = new FluentDOMCoreProxy();
    $nodes = $fd->_getContentFragment($fragment);
    $this->assertType('DOMElement', $nodes[0]);
    $this->assertType('DOMText', $nodes[1]);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentFragment
  */
  public function testGetContentFragmentIncludeTextNodesLimit() {
    $fragment = '<sample/>sample';
    $fd = new FluentDOMCoreProxy();
    $nodes = $fd->_getContentFragment($fragment, TRUE, 1);
    $this->assertType('DOMElement', $nodes[0]);
    $this->assertEquals(1, count($nodes));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentFragment
  */
  public function testGetContentFragmentExcludeTextNodes() {
    $fragment = '<sample/>sample';
    $fd = new FluentDOMCoreProxy();
    $nodes = $fd->_getContentFragment($fragment, FALSE);
    $this->assertType('DOMElement', $nodes[0]);
    $this->assertEquals(1, count($nodes));
  }
  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentFragment
  */
  public function testGetContentFragmentOnlyTextNodes() {
    $fragment = 'sample';
    $fd = new FluentDOMCoreProxy();
    $nodes = $fd->_getContentFragment($fragment);
    $this->assertType('DOMText', $nodes[0]);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentFragment
  */
  public function testGetContentFragmentWithInvalidFragementExpectingException() {
    $fragment = '<sample';
    $fd = new FluentDOMCoreProxy();
    try {
      $nodes = $fd->_getContentFragment('', FALSE);
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithDomelement() {
    $fd = new FluentDOMCoreProxy();
    $node = $fd->document->createElement('sample');
    $this->assertEquals(
      array($node),
      $fd->_getContentNodes($node)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithDomtext() {
    $fd = new FluentDOMCoreProxy();
    $node = $fd->document->createTextNode('sample');
    $this->assertEquals(
      array($node),
      $fd->_getContentNodes($node)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithDomtextIgnoringTextNodesExpectingException() {
    $fd = new FluentDOMCoreProxy();
    $node = $fd->document->createTextNode('sample');
    try {
      $fd->_getContentNodes($node, FALSE);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithString() {
    $fd = new FluentDOMCoreProxy();
    $nodes = $fd->_getContentNodes('sample');
    $this->assertType(
      'DOMText', $nodes[0]
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithStringIgnoringTextNodesExpectingException() {
    $fd = new FluentDOMCoreProxy();
    try {
      $fd->_getContentNodes('sample', FALSE);
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithMixedArray() {
    $fd = new FluentDOMCoreProxy();
    $nodes = array(
      1,
      'foo',
      $elementNode = $fd->document->createElement('sample'),
      $textNode = $fd->document->createTextNode('sample')
    );
    $this->assertEquals(
      array($elementNode, $textNode),
      $fd->_getContentNodes($nodes)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithArrayIgnoringTextNodes() {
    $fd = new FluentDOMCoreProxy();
    $nodes = array(
      $elementNode = $fd->document->createElement('sample'),
      $textNode = $fd->document->createTextNode('sample')
    );
    $this->assertEquals(
      array($elementNode),
      $fd->_getContentNodes($nodes, FALSE)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithArrayAndLimit() {
    $fd = new FluentDOMCoreProxy();
    $nodes = array(
      $elementNode = $fd->document->createElement('sample'),
      $textNode = $fd->document->createTextNode('sample')
    );
    $this->assertEquals(
      array($elementNode),
      $fd->_getContentNodes($nodes, TRUE, 1)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentNodes
  */
  public function testGetContentNodesWithNodeFromAnotherDocument() {
    $fd = new FluentDOMCoreProxy();
    $dom = new DOMDocument();
    $nodes = array(
      $elementNode = $dom->createElement('sample')
    );
    $actual = $fd->_getContentNodes($nodes);
    $this->assertThat(
      $actual[0],
      $this->logicalAnd(
        $this->equalTo($elementNode),
        $this->logicalNot(
          $this->identicalTo($elementNode)
        )
      )
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentElement
  */
  public function testGetContentElementWithDomelement() {
    $fd = new FluentDOMCoreProxy();
    $node = $fd->document->createElement('sample');
    $this->assertSame(
      $node,
      $fd->_getContentElement($node)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContentElement
  */
  public function testGetContentElementWithArray() {
    $fd = new FluentDOMCoreProxy();
    $textNode = $fd->document->createTextNode('sample');
    $elementNode = $fd->document->createElement('sample');
    $this->assertSame(
      $elementNode,
      $fd->_getContentElement(array($textNode, $elementNode))
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContextNodes
  */
  public function testGetContextNodesExpectingCurrentSelection() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//item');
    $this->assertSame(
      $this->readAttribute($fd, '_array'),
      $fd->_getContextNodes()
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getContextNodes
  */
  public function testGetContextNodesWithExpressionExpectingArray() {
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML);
    $this->assertSame(
      $fd->document->documentElement,
      $fd->_getContextNodes('/items')->item(0)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getTargetNodes
  */
  public function testGetTargetNodesWithSingleNodeExpectingArray() {
    $fd = new FluentDOMCoreProxy();
    $node = $fd->document->createElement('sample');
    $this->assertSame(
      array($node),
      $fd->_getTargetNodes($node)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getTargetNodes
  */
  public function testGetTargetNodesWithStringExpectingDomnodelist() {
    $fd = new FluentDOMCoreProxy();
    $node = $fd->document->appendChild($fd->document->createElement('sample'));
    $this->assertSame(
      $node,
      $fd->_getTargetNodes('/sample')->item(0)
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getTargetNodes
  */
  public function testGetTargetNodesWithArrayExpectingArray() {
    $fd = new FluentDOMCoreProxy();
    $node = $fd->document->createElement('sample');
    $this->assertSame(
      array($node),
      $fd->_getTargetNodes(array($node))
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getTargetNodes
  */
  public function testGetTargetNodesWithInvalidSelectorExpectingException() {
    $fd = new FluentDOMCoreProxy();
    try {
      $fd->_getTargetNodes(1);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getInnerXml
  */
  public function testGetInnerXml() {
    $expect = '<item index="0">text1</item>'.
      '<item index="1">text2</item>'.
      '<item index="2">text3</item>';
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//group');
    $this->assertEquals($expect, $fd->_getInnerXml($fd->item(0)));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getInnerXml
  */
  public function testGetInnerXmlOnTextNode() {
    $expect = 'text1';
    $fd = $this->getFluentDOMCoreFixtureFromString(self::XML, '//group/item/text()');
    $this->assertEquals($expect, $fd->_getInnerXml($fd->item(0)));
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_removeNodes
  */
  public function testRemoveNodes() {
    $fd = new FluentDOMCoreProxy();
    $node = $fd->document->appendChild($fd->document->createElement('sample'));
    $actual = $fd->_removeNodes('/sample');
    $this->assertSame(
      array($node), $actual
    );
    $this->assertNull($actual[0]->parentNode);
    $this->assertNull($fd->document->documentElement);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_getHandler
  */
  public function testGetHandler() {
    $fd = new FluentDOMCoreProxy();
    $this->assertEquals(
      'FluentDOMHandler',
      $fd->_getHandler()
    );
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_applyContentToNodes
  */
  public function testApplyContentToNodes() {
    $fd = new FluentDOMCoreProxy();
    $fd->document->appendChild($fd->document->createElement('sample'));
    $result = $fd->_applyContentToNodes(
      array($fd->document->documentElement),
      'Hello World',
      array($this, 'callbackHandlerForApplyContentToNodes')
    );
    $this->assertSame(
      $fd->document->documentElement->childNodes->item(0), $result[0]
    );
    $this->assertEquals('Hello World', $result[0]->textContent);
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_applyContentToNodes
  * @covers FluentDOMCore::_executeEasySetter
  */
  public function testApplyContentToNodesWithEasySetter() {
    $fd = new FluentDOMCoreProxy();
    $fd->document->appendChild(
      $fd->document->createElement('sample')
    );
    $fd->document->documentElement->appendChild(
      $fd->document->createTextNode('Hello World!')
    );
    $result = $fd->_applyContentToNodes(
      array($fd->document->documentElement),
      array($this, 'callbackEasySetterForApplyContentToNodes'),
      array($this, 'callbackHandlerForApplyContentToNodes')
    );
  }

  public function callbackHandlerForApplyContentToNodes($targetNode, $contentNodes) {
    $targetNode->appendChild($contentNodes[0]);
    return $contentNodes;
  }

  public function callbackEasySetterForApplyContentToNodes($node, $index, $value) {
    $this->assertType('DOMElement', $node);
    $this->assertEquals(0, $index);
    $this->assertEquals('Hello World!', $value);
    return ' Hi Earth!';
  }

  /**
  * @group CoreFunctions
  * @covers FluentDOMCore::_executeEasySetter
  */
  public function testExecuteEasySetterExpectingEmptyArray() {
    $fd = new FluentDOMCoreProxy();
    $this->assertSame(
      array(),
      $fd->_executeEasySetter(
        array($this, 'callbackEasySetterForEasySetterExpectingEmptyArray'),
        NULL,
        0,
        ''
      )
    );
  }

  public function callbackEasySetterForEasySetterExpectingEmptyArray($node, $index, $value) {
    return NULL;
  }

  /******************************
  * Fixtures
  ******************************/

  function getFluentDOMCoreFixtureFromString($string = NULL, $xpath = NULL) {
    $fd = new FluentDOMCoreProxy();
    if (!empty($string)) {
      $dom = new DOMDocument();
      $dom->loadXML($string);
      $loader = $this->getMock('FluentDOMLoader');
      $loader
        ->expects($this->once())
        ->method('load')
        ->with($this->equalTo(''))
        ->will($this->returnValue($dom));
      $fd->setLoaders(array($loader));
      $fd->load('');
      if (!empty($xpath)) {
        $query = new DOMXPath($dom);
        $nodes = $query->evaluate($xpath);
        $fd = $fd->spawn();
        $fd->push($nodes);
      }
    }
    return $fd;
  }
}

/******************************
* Proxy
******************************/

class FluentDOMCoreProxy extends FluentDOMCore {

  public function _match($expr, $context = NULL) {
    return parent::_match($expr, $context);
  }

  public function _test($expr, $context = NULL) {
    return parent::_test($expr, $context);
  }

  public function _uniqueSort() {
    return parent::_uniqueSort();
  }

  public function _inList($node) {
    return parent::_inList($node);
  }

  public function _isQName($name) {
    return parent::_isQName($name);
  }

  public function _isNCName($name, $offset = 0, $length = 0) {
    return parent::_isNCName($name, $offset, $length);
  }

  public function _isNode($node, $ignoreTextNodes = FALSE) {
    return parent::_isNode($node, $ignoreTextNodes);
  }

  public function _isNodeList($elements) {
    return parent::_isNodeList($elements);
  }

  public function _isCallback($callback, $allowGlobalFunctions, $silent) {
    return parent::_isCallback($callback, $allowGlobalFunctions, $silent);
  }

  public function _getContentFragment($content, $includeTextNodes = TRUE, $limit = 0) {
    return parent::_getContentFragment($content, $includeTextNodes, $limit);
  }

  public function _getContentNodes($content, $includeTextNodes = TRUE, $limit = 0) {
    return parent:: _getContentNodes($content, $includeTextNodes, $limit);
  }

  public function _getContentElement($content) {
    return parent::_getContentElement($content);
  }

  public function _getContextNodes($selector = NULL) {
    return parent::_getContextNodes($selector);
  }

  public function _getTargetNodes($selector) {
    return parent::_getTargetNodes($selector);
  }

  public function _getInnerXml($node) {
    return parent::_getInnerXml($node);
  }

  public function _removeNodes($selector) {
    return parent::_removeNodes($selector);
  }

  public function _getHandler() {
    return parent::_getHandler();
  }

  public function _applyContentToNodes($targetNodes, $content, $handler) {
    return parent::_applyContentToNodes($targetNodes, $content, $handler);
  }

  public function _executeEasySetter($easySetter, $node, $index, $value) {
    return parent::_executeEasySetter($easySetter, $node, $index, $value);
  }
}