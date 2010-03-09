<?php
/**
* Collection of tests for the FluentDOMCore class
*
* @version $Id: FluentDOMTest.php 374 2010-01-18 11:02:58Z subjective $
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
  public function testGetPropertyXpathDefaultNamespaceInitialization() {
    $fd = $this->getFluentDOMCoreFixtureFromString('<sample xmlns="http://sample.tld/"/>');
    $this->assertEquals(
      1,
      $fd->xpath->evaluate('count(//_:*)')
    );
  }

  /**
  * @group Properties
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
  public function testPushWithArrayContaining() {
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

  public function __call($functionName, $arguments) {
    if (method_exists($this, $functionName)) {
      return call_user_func_array(array($this, $functionName), $arguments);
    } else {
      trigger_error(
         sprintf(
           'Call to undefined method %s::%s',
           __CLASS__,
           $functionName
         ),
         E_USER_ERROR
       );
    }
  }
}