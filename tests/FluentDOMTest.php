<?php
/**
* Collection of test for the FluentDOM class supporting PHP 5.2
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
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../FluentDOM.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOM.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMTest extends PHPUnit_Framework_TestCase {

  const XML = '
    <items version="1.0">
      <group>
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
  * directory of this file
  * @var string
  */
  private $_directory = '';

  /**
  * initialize test suite
  *
  * @access public
  * @return
  */
  function setUp() {
    $this->_directory = dirname(__FILE__);
  }

  /*
  * Load
  */
  
  /**
  * @group Load
  */
  function testLoadWithInvalidSource() {
    $doc = new FluentDOM();
    try {
      $doc->load(1);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
   * @group Load
   */
  function testLoaderMechanism() {
      $firstLoaderMock = $this->getMock('FluentDOMLoader');
      $firstLoaderMock->expects($this->once())
                      ->method('load')
                      ->with($this->equalTo('test load string'), $this->equalTo('xml'))
                      ->will($this->returnValue(FALSE));
      $secondLoaderMock = $this->getMock('FluentDOMLoader');
      $secondLoaderMock->expects($this->once())
                       ->method('load')
                       ->with($this->equalTo('test load string'), $this->equalTo('xml'))
                       ->will($this->returnValue(new DOMDocument()));

      $fd = new FluentDOM();
      $fd->setLoaders(array($firstLoaderMock, $secondLoaderMock));

      $this->assertSame(
          $fd,
          $fd->load('test load string')
      );
  }

  /*
  * Properties
  */

  /**
  *
  * @group Properties
  */
  function testPropertyDocument() {
    $doc = FluentDOM(self::XML);
    $this->assertTrue(isset($doc->document));
    $this->assertTrue($doc->document instanceof DOMDocument);
    try {
      $doc->document = NULL;
    } catch (BadMethodCallException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group Properties
  */
  function testPropertyXPath() {
    $doc = FluentDOM(self::XML);
    $this->assertTrue(isset($doc->xpath));
    $this->assertTrue($doc->xpath instanceof DOMXPath);
    try {
      $doc->xpath = NULL;
    } catch (BadMethodCallException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group Properties
  */
  function testPropertyLength() {
    $doc = FluentDOM(self::XML);
    $this->assertTrue(isset($doc->length));
    $this->assertEquals(0, $doc->length);
    $doc = $doc->find('/items');
    $this->assertEquals(1, $doc->length);
    try {
      $doc->length = 50;
    } catch (BadMethodCallException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group Properties
  */
  function testDynamicProperty() {
    $doc = FluentDOM(self::XML);
    $this->assertEquals(FALSE, isset($doc->dynamicProperty));
    $this->assertEquals(NULL, $doc->dynamicProperty);
    $doc->dynamicProperty = 'test';
    $this->assertEquals(TRUE, isset($doc->dynamicProperty));
    $this->assertEquals('test', $doc->dynamicProperty);
  }

  /*
  * __toString() method
  */

  /**
  *
  * @group MagicFunctions
  */
  function testMagicToString() {
    $doc = FluentDOM(self::XML);
    $this->assertEquals($doc->document->saveXML(), (string)$doc);
  }

  /**
  *
  * @group MagicFunctions
  */
  function testMagicToStringHTML() {
    $doc = FluentDOM('<html><body><br></body></html>', 'html');
    $this->assertEquals('br', $doc->find('//br')->item(0)->nodeName);
    $this->assertEquals($doc->document->saveHTML(), (string)$doc);
  }

  /**
  *
  * @group MagicFunctions
  */
  function testMagicCallUnknown() {
    try {
      FluentDOM(self::XML)->invalidDynamicMethodName();
    } catch (BadMethodCallException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');

  }

  /*
  * Interfaces
  */

  /**
  *
  * @group Interfaces
  */
  function testInterfaceArrayAccessIsset() {
    $items = FluentDOM(self::XML)->find('//item');
    $this->assertTrue($items instanceof ArrayAccess);
    $this->assertEquals(TRUE, isset($items[1]));
    $this->assertEquals(FALSE, isset($items[200]));
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceArrayAccessGet() {
    $items = FluentDOM(self::XML)->find('//item');
    $this->assertTrue($items instanceof ArrayAccess);
    $this->assertEquals('item', $items[1]->nodeName);
    $this->assertEquals(1, $items[1]->getAttribute('index'));
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceArrayAccessSet() {
    $items = FluentDOM(self::XML)->find('//item');
    $this->assertTrue($items instanceof ArrayAccess);
    try {
      $items[1] = NULL;
    } catch (BadMethodCallException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceArrayAccessUnset() {
    $items = FluentDOM(self::XML)->find('//item');
    $this->assertTrue($items instanceof ArrayAccess);
    try {
      unset($items[1]);
    } catch (BadMethodCallException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceCountable() {
    $doc = FluentDOM(self::XML);
    $this->assertTrue($doc instanceof Countable);
    $this->assertEquals(0, count($doc));
    $items = $doc->find('//item');
    $this->assertEquals(3, count($items));
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceIteratorMethods() {
    $items = FluentDOM(self::XML)->find('//item');
    $this->assertTrue($items instanceof Iterator);
    $this->assertEquals(0, $items->current()->getAttribute('index'));
    $items->next();
    $this->assertEquals(1, $items->current()->getAttribute('index'));
    $this->assertEquals(1, $items->key());
    $items->rewind();
    $this->assertEquals(0, $items->current()->getAttribute('index'));
    $this->assertEquals(0, $items->key());
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceIteratorLoop() {
    $items = FluentDOM(self::XML)->find('//item');
    $this->assertTrue($items instanceof Iterator);
    $counter = 0;
    foreach ($items as $item) {
      $this->assertEquals('item', $item->nodeName);
      $this->assertEquals($counter, $item->getAttribute('index'));
      ++$counter;
    }
    $this->assertEquals(3, $counter);
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceSeekableIterator() {
    $items = FluentDOM(self::XML)->find('//item');
    $this->assertTrue($items instanceof SeekableIterator);
    $this->assertEquals(0, $items->key());
    $items->seek(2);
    $this->assertEquals(2, $items->key());
    try {
      $items->seek(200);
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceRecursiveIterator() {
    $iterator = new RecursiveIteratorIterator(
      FluentDOM(self::XML)->find('/*'),
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

  /*
  * Core functions
  */

  /**
  *
  * @group CoreFunctions
  */
  function testItem() {
    $doc = FluentDOM(self::XML);
    $doc = $doc->find('/items');
    $this->assertEquals($doc->document->documentElement, $doc->item(0));
    $this->assertEquals(NULL, $doc->item(-10));
  }

  /**
  *
  * @group CoreFunctions
  */
  function testEach() {
    $this->assertFileExists($this->_directory.'/data/each.src.xml');
    $dom = FluentDOM($this->_directory.'/data/each.src.xml')
      ->find('//body//*')
      ->each(
        create_function(
          '$node, $item',
          '$fluentNode = FluentDOM($node);
           $fluentNode->prepend("EACH > ");
          ')
      );
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/each.tgt.xml', $dom);
  }

  /**
  *
  * @group CoreFunctions
  */
  function testEachWithInvalidFunction() {
    try {
      $dom = FluentDOM(self::XML)
        ->find('//body//*')
        ->each('invalidCallbackFunctionName');
    } catch (BadFunctionCallException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNode() {
    $this->assertFileExists($this->_directory.'/data/node.src.xml');
    $doc = FluentDOM($this->_directory.'/data/node.src.xml')
      ->node(
        FluentDOM('<samples>
                    <b id="first">Paragraph. </b>
                  </samples>')
          ->find('//b[@id = "first"]')
          ->removeAttr('id')
          ->addClass('imported')
      )
      ->replaceAll('//p');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/node.tgt.xml', $doc);
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithDOMElement() {
    $doc = FluentDOM(self::XML);
    $nodes = $doc->node($doc->document->createElement('div'));
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertEquals(1, count($nodes));
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithDOMText() {
    $doc = FluentDOM(self::XML);
    $nodes = $doc->node($doc->document->createTextNode('div'));
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertEquals(1, count($nodes));
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithInvalidContent() {
    try {
      $dom = FluentDOM(self::XML)
        ->node(NULL);
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithEmptyContent() {
    try {
      $dom = FluentDOM(self::XML)
        ->node('');
    } catch (UnexpectedValueException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithEmptyList() {
    try {
      $dom = FluentDOM(self::XML);
      $dom->node(
          $dom->find('UnknownTagName')
        );
    } catch (UnexpectedValueException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
  }

  /*
  * Traversing - Filtering
  */

  /**
  *
  * @group TraversingFilter
  */
  function testEq() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $eqDoc = $doc->eq(0);
    $this->assertEquals(1, $eqDoc->length);
    $this->assertTrue($eqDoc !== $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testFilter() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $filterDoc = $doc->filter('name() = "items"');
    $this->assertEquals(1, $filterDoc->length);
    $this->assertTrue($filterDoc !== $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testFilterWithFunction() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $filterDoc = $doc->filter(array($this, 'callbackTestFilterWithFunction'));
    $this->assertEquals(1, $filterDoc->length);
    $this->assertTrue($filterDoc !== $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testIs() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $this->assertTrue($doc->is('name() = "items"'));
    $this->assertFalse($doc->is('name() = "invalidItemName"'));
  }

  /**
  *
  * @group TraversingFilter
  */
  function testIsOnEmptyList() {
    $doc = FluentDOM(self::XML);
    $this->assertTrue($doc->length == 0);
    $this->assertFalse($doc->is('name() = "items"'));
  }

  /**
  *
  * @group TraversingFilter
  */
  function testMap() {
    $this->assertFileExists($this->_directory.'/data/map.src.xml');
    $dom = FluentDOM($this->_directory.'/data/map.src.xml');
    $dom->find('//p')
      ->append(
        implode(
          ', ',
          $dom
            ->find('//input')
            ->map(
              create_function('$node, $index', 'return FluentDOM($node)->attr("value");')
            )
        )
      );
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/map.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testMapMixedResult() {
    $this->assertFileExists($this->_directory.'/data/mapMixedResult.src.xml');
    $dom = FluentDOM($this->_directory.'/data/mapMixedResult.src.xml');
    $dom->find('//p')
      ->append(
        implode(
          ', ',
          $dom
            ->find('//input')
            ->map(
              create_function(
                '$node, $index',
                '
                  switch($index) {
                  case 0:
                    return NULL;
                  case 1:
                    return 3;
                  default:
                    return array(1,2);
                  }
                ')
            )
        )
      );
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/mapMixedResult.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testMapInvalidCallback() {
    $this->assertFileExists($this->_directory.'/data/map.src.xml');
    $doc = FluentDOM($this->_directory.'/data/map.src.xml');
    try {
      $doc->find('//p')
        ->map('invalidCallbackFunctionName');
    } catch (BadFunctionCallException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group TraversingFilter
  */
  function testNot() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $notDoc = $doc->not('name() != "items"');
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testNotWithFunction() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $notDoc = $doc->not(array($this, 'callbackTestNotWithFunction'));
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceByRangeStartLtEnd() {
    $this->assertFileExists($this->_directory.'/data/sliceByRangeStartLtEnd.src.xml');
    $doc = FluentDOM($this->_directory.'/data/sliceByRangeStartLtEnd.src.xml')
      ->find('//p')
      ->slice(0,3)
      ->replaceAll('//div');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/sliceByRangeStartLtEnd.tgt.xml', $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceByRangeStartGtEnd() {
    $this->assertFileExists($this->_directory.'/data/sliceByRangeStartGtEnd.src.xml');
    $doc = FluentDOM($this->_directory.'/data/sliceByRangeStartGtEnd.src.xml')
      ->find('//p')
      ->slice(5,2)
      ->replaceAll('//div');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/sliceByRangeStartGtEnd.tgt.xml', $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceByNegRange() {
    $this->assertFileExists($this->_directory.'/data/sliceByNegRange.src.xml');
    $doc = FluentDOM($this->_directory.'/data/sliceByNegRange.src.xml')
      ->find('//p')
      ->slice(1,-2)
      ->replaceAll('//div');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/sliceByNegRange.tgt.xml', $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceToEnd() {
    $this->assertFileExists($this->_directory.'/data/sliceToEnd.src.xml');
    $doc = FluentDOM($this->_directory.'/data/sliceToEnd.src.xml')
      ->find('//p')
      ->slice(3)
      ->replaceAll('//div');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/sliceToEnd.tgt.xml', $doc);
  }

  /*
  * Traversing - Finding
  */

  /**
  *
  * @group TraversingFind
  */
  function testAddElements() {
    $this->assertFileExists($this->_directory.'/data/addElements.src.xml');
    $dom = FluentDOM($this->_directory.'/data/addElements.src.xml');
    $dom
      ->add(
        $dom->find('//div')
      )
      ->toggleClass('inB');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/addElements.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testAddFromExpression() {
    $this->assertFileExists($this->_directory.'/data/addFromExpression.src.xml');
    $dom = FluentDOM($this->_directory.'/data/addFromExpression.src.xml');
    $dom
      ->add('//div')
      ->toggleClass('inB');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/addFromExpression.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testAddInContext() {
    $this->assertFileExists($this->_directory.'/data/addInContext.src.xml');
    $dom = FluentDOM($this->_directory.'/data/addInContext.src.xml');
    $dom
      ->find('//p')
      ->add('//p/b')
      ->toggleClass('inB');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/addInContext.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testInvalidAddForgeinNodes() {
    $dom = FluentDOM(self::XML);
    $items = FluentDOM(self::XML)->find('//item');
    try {
      $dom
        ->find('/items')
        ->add($items);
    } catch (OutOfBoundsException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testInvalidAddForgeinNode() {
    $dom = FluentDOM(self::XML);
    $items = FluentDOM(self::XML)->find('//item');
    try {
      $dom
        ->find('/items')
        ->add($items[0]);
    } catch (OutOfBoundsException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testChildren() {
    $this->assertFileExists($this->_directory.'/data/children.src.xml');
    $dom = FluentDOM($this->_directory.'/data/children.src.xml')
      ->find('//div[@id = "container"]/p')
      ->children()
      ->toggleClass('child');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/children.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testChildrenExpression() {
    $this->assertFileExists($this->_directory.'/data/childrenExpression.src.xml');
    $dom = FluentDOM($this->_directory.'/data/childrenExpression.src.xml')
      ->find('//div[@id = "container"]/p')
      ->children('name() = "em"')
      ->toggleClass('child');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/childrenExpression.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testFind() {
    $doc = FluentDOM(self::XML)->find('/*');
    $this->assertEquals(1, $doc->length);
    $findDoc = $doc->find('group/item');
    $this->assertEquals(3, $findDoc->length);
    $this->assertTrue($findDoc !== $doc);
  }

  /**
  *
  * @group TraversingFind
  */
  function testFindFromRootNode() {
    $doc = FluentDOM(self::XML)->find('/*');
    $this->assertEquals(1, $doc->length);
    $findDoc = FluentDOM(self::XML)->find('/items');
    $this->assertEquals(1, $findDoc->length);
    $this->assertTrue($findDoc == $doc);
  }

  /**
  *
  * @group TraversingFind
  */
  function testFindWithNamespaces() {
    $this->assertFileExists($this->_directory.'/data/findWithNamespaces.src.xml');
    $doc = FluentDOM($this->_directory.'/data/findWithNamespaces.src.xml')->find('//_:entry');
    $this->assertEquals(25, $doc->length);
    $value = FluentDOM($this->_directory.'/data/findWithNamespaces.src.xml')->find('//openSearch:totalResults')->text();
    $this->assertEquals(38, $value);
  }

  /**
  *
  * @group TraversingFind
  */
  function testNextSiblings() {
    $this->assertFileExists($this->_directory.'/data/nextSiblings.src.xml');
    $dom = FluentDOM($this->_directory.'/data/nextSiblings.src.xml')
      ->find('//button[@disabled]')
      ->nextSiblings()
      ->text('This button is disabled.');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/nextSiblings.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testNextAllSiblings() {
    $this->assertFileExists($this->_directory.'/data/nextAllSiblings.src.xml');
    $dom = FluentDOM($this->_directory.'/data/nextAllSiblings.src.xml')
      ->find('//div[position() = 1]')
      ->nextAllSiblings()
      ->addClass('after');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/nextAllSiblings.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testParent() {
    $this->assertFileExists($this->_directory.'/data/parent.src.xml');
    $dom = FluentDOM($this->_directory.'/data/parent.src.xml')
      ->find('//body//*')
      ->each(
        create_function(
          '$node, $item',
          '$fluentNode = FluentDOM($node);
           $fluentNode->prepend(
             $fluentNode->document->createTextNode(
               $fluentNode->parent()->item(0)->tagName." > "
             )
            );
          ')
      );
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/parent.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testParents() {
    $this->assertFileExists($this->_directory.'/data/parent.src.xml');
    $dom = FluentDOM($this->_directory.'/data/parents.src.xml');
    $this->assertTrue($dom instanceof FluentDOM);
    $parents = $dom
      ->find('//b')
      ->parents()
      ->map(
          create_function('$node', 'return $node->tagName;')
        );
    $this->assertTrue(is_array($parents));
    $this->assertContains('span', $parents);
    $this->assertContains('p', $parents);
    $this->assertContains('div', $parents);
    $this->assertContains('body', $parents);
    $this->assertContains('html', $parents);
    $parents = implode(', ', $parents);
    $doc = $dom
      ->find('//b')
      ->append('<strong>'.htmlspecialchars($parents).'</strong>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/parents.tgt.xml', $doc);
  }

  /**
  *
  * @group TraversingFind
  */
  function testPrevSiblings() {
    $this->assertFileExists($this->_directory.'/data/prevSiblings.src.xml');
    $dom = FluentDOM($this->_directory.'/data/prevSiblings.src.xml')
      ->find('//div[@id = "start"]')
      ->prevSiblings()
      ->addClass('before');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/prevSiblings.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testPrevSiblingsExpression() {
    $this->assertFileExists($this->_directory.'/data/prevSiblingsExpression.src.xml');
    $dom = FluentDOM($this->_directory.'/data/prevSiblingsExpression.src.xml')
      ->find('//div[@class = "here"]')
      ->prevSiblings()
      ->addClass('nextTest');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/prevSiblingsExpression.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testPrevAllSiblings() {
    $this->assertFileExists($this->_directory.'/data/prevAllSiblings.src.xml');
    $dom = FluentDOM($this->_directory.'/data/prevAllSiblings.src.xml')
      ->find('//div[@id = "start"]')
      ->prevSiblings()
      ->addClass('before');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/prevAllSiblings.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testPrevAllSiblingsExpression() {
    $this->assertFileExists($this->_directory.'/data/prevAllSiblingsExpression.src.xml');
    $dom = FluentDOM($this->_directory.'/data/prevAllSiblingsExpression.src.xml')
      ->find('//div[@class= "here"]')
      ->prevAllSiblings('.//span')
      ->addClass('nextTest');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/prevAllSiblingsExpression.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingFind
  */
  function testSiblings() {
    $this->assertFileExists($this->_directory.'/data/siblings.src.xml');
    $dom = FluentDOM($this->_directory.'/data/siblings.src.xml')
      ->find('//li[@class = "hilite"]')
      ->siblings()
      ->addClass('before');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/siblings.tgt.xml', $dom);
  }

  /*
  * Traversing - Chaining
  */

  /**
  *
  * @group TraversingChain
  */
  function testAndSelf() {
    $doc = FluentDOM(self::XML)->find('/items')->find('.//item');
    $this->assertEquals(3, $doc->length);
    $andSelfDoc = $doc->andSelf();
    $this->assertEquals(4, $andSelfDoc->length);
    $this->assertTrue($andSelfDoc !== $doc);
  }

  /**
  *
  * @group TraversingChain
  */
  function testEnd() {
    $doc = FluentDOM(self::XML)->find('/items')->find('.//item');
    $this->assertEquals(3, $doc->length);
    $endDoc = $doc->end();
    $this->assertEquals(1, $endDoc->length);
    $this->assertTrue($endDoc !== $doc);
    $endDocRoot = $endDoc->end();
    $this->assertTrue($endDoc !== $endDocRoot);
    $endDocRoot2 = $endDocRoot->end();
    $this->assertTrue($endDocRoot === $endDocRoot2);
  }

  /**
  *
  * @group TraversingChain
  */
  function testXMLRead() {
    $expect = '<item index="0">text1</item>'.
      '<item index="1">text2</item>'.
      '<item index="2">text3</item>';
    $xml = FluentDOM(self::XML)->find('//group')->xml();
    $this->assertEquals($expect, $xml);
  }

  /**
  *
  * @group TraversingChain
  */
  function testXMLWrite() {
    $this->assertFileExists($this->_directory.'/data/xmlWrite.src.xml');
    $dom = FluentDOM($this->_directory.'/data/xmlWrite.src.xml')
      ->find('//p[position() = last()]')
      ->xml('<b>New</b>World');
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/xmlWrite.tgt.xml', $dom);
  }

  /**
  *
  * @group TraversingChain
  */
  function testTextRead() {
    $expect = 'text1text2text3';
    $text = FluentDOM(self::XML)->formatOutput()->find('//group')->text();
    $this->assertEquals($expect, $text);
  }

  /**
  *
  * @group TraversingChain
  */
  function testTextWrite() {
    $doc = FluentDOM(self::XML)->find('//item');
    $this->assertEquals('text1', $doc[0]->textContent);
    $this->assertEquals('text2', $doc[1]->textContent);
    $textDoc = $doc->text('changed');
    $this->assertEquals('changed', $doc[0]->textContent);
    $this->assertEquals('changed', $doc[1]->textContent);
    $this->assertTrue($doc === $textDoc);
  }

  /*
  * Manipulation - Inserting Inside
  */

  /**
  *
  * @group Manipulation
  */
  function testAppend() {
    $this->assertFileExists($this->_directory.'/data/append.src.xml');
    $doc = FluentDOM($this->_directory.'/data/append.src.xml')
      ->find('//p')
      ->append('<strong>Hello</strong>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/append.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testAppendDocumentElement() {
    $doc = FluentDOM()
      ->append('<strong>Hello</strong>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertEquals('strong', $doc->find('/strong')->item(0)->nodeName);
  }

  /**
  *
  * @group Manipulation
  */
  function testAppendDOMNodeList() {
    $this->assertFileExists($this->_directory.'/data/appendDOMNodeList.src.xml');
    $dom = FluentDOM($this->_directory.'/data/appendDOMNodeList.src.xml');
    $items = $dom->find('//item');
    $this->assertTrue($dom instanceof FluentDOM);
    $doc = $dom
      ->find('//html/div')
      ->append($items);
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/appendDOMNodeList.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testAppendTo() {
    $this->assertFileExists($this->_directory.'/data/appendTo.src.xml');
    $doc = FluentDOM($this->_directory.'/data/appendTo.src.xml')
      ->find('//span')
      ->appendTo('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/appendTo.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testPrepend() {
    $this->assertFileExists($this->_directory.'/data/prepend.src.xml');
    $doc = FluentDOM($this->_directory.'/data/prepend.src.xml')
      ->find('//p')
      ->prepend('<strong>Hello</strong>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/prepend.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testPrependTo() {
    $this->assertFileExists($this->_directory.'/data/prependTo.src.xml');
    $doc = FluentDOM($this->_directory.'/data/prependTo.src.xml')
      ->find('//span')
      ->prependTo('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/prependTo.tgt.xml', $doc);
  }

  /*
  * Manipulation - Inserting Outside
  */

  /**
  *
  * @group Manipulation
  */
  function testAfter() {
    $this->assertFileExists($this->_directory.'/data/after.src.xml');
    $doc = FluentDOM($this->_directory.'/data/after.src.xml')
      ->formatOutput()
      ->find('//p')
      ->after('<b>Hello</b>')
      ->after(' World');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/after.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testBefore() {
    $this->assertFileExists($this->_directory.'/data/before.src.xml');
    $doc = FluentDOM($this->_directory.'/data/before.src.xml')
      ->formatOutput()
      ->find('//p')
      ->before(' World')
      ->before('<b>Hello</b>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/before.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testInsertAfter() {
    $this->assertFileExists($this->_directory.'/data/insertAfter.src.xml');
    $doc = FluentDOM($this->_directory.'/data/insertAfter.src.xml')
      ->find('//p')
      ->insertAfter('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/insertAfter.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testInsertBefore() {
    $this->assertFileExists($this->_directory.'/data/insertBefore.src.xml');
    $doc = FluentDOM($this->_directory.'/data/insertBefore.src.xml')
      ->find('//p')
      ->insertBefore('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/insertBefore.tgt.xml', $doc);
  }

  /*
  * Manipulation - Inserting Around
  */

  /**
  *
  * @group Manipulation
  */
  function testWrap() {
    $this->assertFileExists($this->_directory.'/data/wrap.src.xml');
    $doc = FluentDOM($this->_directory.'/data/wrap.src.xml')
      ->find('//p')
      ->wrap('<div class="outer"><div class="inner"></div></div>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/wrap.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapWithDOMElement() {
    $this->assertFileExists($this->_directory.'/data/wrapWithDOMElement.src.xml');
    $doc = FluentDOM($this->_directory.'/data/wrapWithDOMElement.src.xml');
    $dom = $doc->document;
    $div = $dom->createElement('div');
    $div->setAttribute('class', 'wrapper');
    $doc->find('//p')->wrap($div);
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/wrapWithDOMElement.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapWithDOMNodeList() {
    $this->assertFileExists($this->_directory.'/data/wrapWithDOMNodeList.src.xml');
    $doc = FluentDOM($this->_directory.'/data/wrapWithDOMNodeList.src.xml');
    $divs = $doc->xpath->query('//div[@class = "wrapper"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $doc->find('//p')->wrap($divs);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/wrapWithDOMNodeList.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapWithInvalidArgument() {
    try {
      FluentDOM(self::XML)
        ->find('//item')
        ->wrap(NULL);
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapWithArray() {
    $this->assertFileExists($this->_directory.'/data/wrapWithArray.src.xml');
    $doc = FluentDOM($this->_directory.'/data/wrapWithArray.src.xml');
    $dom = $doc->document;
    $divs[0] = $dom->createElement('div');
    $divs[0]->setAttribute('class', 'wrapper');
    $divs[1] = $dom->createElement('div');
    $doc->find('//p')->wrap($divs);
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/wrapWithArray.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapAllSingle() {
    $this->assertFileExists($this->_directory.'/data/wrapAllSingle.src.xml');
    $doc = FluentDOM($this->_directory.'/data/wrapAllSingle.src.xml')
      ->find('//p')
      ->wrapAll('<div class="wrapper"/>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/wrapAllSingle.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapAllComplex() {
    $this->assertFileExists($this->_directory.'/data/wrapAllComplex.src.xml');
    $doc = FluentDOM($this->_directory.'/data/wrapAllComplex.src.xml')
      ->find('//p')
      ->wrapAll('<div class="wrapper"><div>INNER</div></div>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/wrapAllComplex.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapInner() {
    $this->assertFileExists($this->_directory.'/data/wrapInner.src.xml');
    $doc = FluentDOM($this->_directory.'/data/wrapInner.src.xml')
      ->find('//p')
      ->wrapInner('<b></b>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/wrapInner.tgt.xml', $doc);
  }

  /*
  * Manipulation - Replacing
  */

  /**
  *
  * @group Manipulation
  */
  function testReplaceWith() {
    $this->assertFileExists($this->_directory.'/data/replaceWith.src.xml');
    $doc = FluentDOM($this->_directory.'/data/replaceWith.src.xml')
      ->find('//p')
      ->replaceWith('<b>Paragraph. </b>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/replaceWith.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testReplaceAll() {
    $this->assertFileExists($this->_directory.'/data/replaceAll.src.xml');
    $doc = FluentDOM($this->_directory.'/data/replaceAll.src.xml')
      ->node('<b id="sample">Paragraph. </b>')
      ->replaceAll('//p');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/replaceAll.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testReplaceAllWithNode() {
    $this->assertFileExists($this->_directory.'/data/replaceAllWithNode.src.xml');
    $doc = FluentDOM($this->_directory.'/data/replaceAllWithNode.src.xml');
    $doc->node('<b id="sample">Paragraph. </b>')
      ->replaceAll(
        $doc->find('//p')->item(1)
      );
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/replaceAllWithNode.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testReplaceAllWithInvalidArgument() {
    try {
      $doc = FluentDOM(self::XML);
      $doc->node('<b id="sample">Paragraph. </b>')
        ->replaceAll(
          NULL
        );
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /*
  * Manipulation - Removing
  */

  /**
  *
  * @group Manipulation
  */
  function testEmpty() {
    $this->assertFileExists($this->_directory.'/data/empty.src.xml');
    $doc = FluentDOM($this->_directory.'/data/empty.src.xml')
      ->find('//p[@class = "first"]')
      ->empty();
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/empty.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testRemove() {
    $this->assertFileExists($this->_directory.'/data/remove.src.xml');
    $doc = FluentDOM($this->_directory.'/data/remove.src.xml')
      ->find('//p[@class = "first"]')
      ->remove();
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/remove.tgt.xml', $doc);
  }

  /*
  * Manipulation - Copying
  */

  /**
  *
  * @group Manipulation
  */
  function testClone() {
    $doc = FluentDOM(self::XML)->find('//item');
    $clonedNodes = $doc->clone();
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertTrue($clonedNodes instanceof FluentDOM);
    $this->assertTrue($doc[0] !== $clonedNodes[0]);
    $this->assertEquals($doc[0]->nodeName, $clonedNodes[0]->nodeName);
    $this->assertEquals($doc[1]->getAttribute('index'), $clonedNodes[1]->getAttribute('index'));
    $this->assertEquals(count($doc), count($clonedNodes));
  }


  /*
  * Attributes
  */

  /**
  *
  * @group Attributes
  */
  function testAttrRead() {
    $doc = FluentDOM(self::XML)
      ->find('//group/item')
      ->attr('index');
    $this->assertEquals('0', $doc);
  }
  /**
  *
  * @group Attributes
  */
  function testAttrReadFromRoot() {
    $doc = FluentDOM(self::XML)
      ->find('/*')
      ->attr('version');
    $this->assertEquals('1.0', $doc);
    $doc = FluentDOM(self::XML)
      ->find('/items')
      ->attr('version');
    $this->assertEquals('1.0', $doc);
    $doc = FluentDOM(self::XML)
      ->find('//items')
      ->attr('version');
    $this->assertEquals('1.0', $doc);
  }

  /**
  *
  * @group Attributes
  */
  function testAttrReadInvalid() {
    try {
      FluentDOM(self::XML)
        ->find('//item')
        ->attr('');
    } catch (UnexpectedValueException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }

  /**
  *
  * @group Attributes
  */
  function testAttrReadNoMatch() {
    $doc = FluentDOM(self::XML)
      ->attr('index');
    $this->assertTrue(empty($doc));
  }

  /**
  *
  * @group Attributes
  */
  function testAttrReadOnDOMText() {
    $doc = FluentDOM(self::XML)
      ->find('//item/text()')
      ->attr('index');
    $this->assertTrue(empty($doc));
  }

  /**
  *
  * @group Attributes
  */
  function testAttrWrite() {
    $doc = FluentDOM(self::XML)
      ->find('//group/item')
      ->attr('index', '15')
      ->attr('index');
    $this->assertEquals('15', $doc);

  }

  /**
  *
  * @group Attributes
  */
  function testAttrWriteArray() {
    $doc = FluentDOM(self::XML)
      ->find('//group/item')
      ->attr(array('index' => '15', 'length' => '34', 'label' => 'box'));
    $this->assertEquals('15', $doc->attr('index'));
    $this->assertEquals('34', $doc->attr('length'));
    $this->assertEquals('box', $doc->attr('label'));
  }

  /**
  *
  * @group Attributes
  */
  function testAttrWriteCallback() {
    $doc = FluentDOM(self::XML)
      ->find('//group/item')
      ->attr('callback', array($this, 'callbackForAttr'));
    $this->assertEquals($doc[0]->nodeName, $doc->attr('callback'));
  }

  /**
  *
  * @group Attributes
  */
  function testRemoveAttr() {
    $this->assertFileExists($this->_directory.'/data/removeAttr.src.xml');
    $doc = FluentDOM($this->_directory.'/data/removeAttr.src.xml')
      ->find('//p')
      ->removeAttr('index');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile($this->_directory.'/data/removeAttr.tgt.xml', $doc);
  }

  /*
  * Attributes - Classes
  */

  /**
  *
  * @group Attributes
  */
  function testAddClass() {
    $doc = FluentDOM(self::XML)->find('//html/div');
    $this->assertTrue($doc->hasClass('added') === FALSE);
    $doc->addClass('added');
    $this->assertTrue($doc->hasClass('added') === TRUE);
  }

  /**
  *
  * @group Attributes
  */
  function testHasClass() {
    $doc = FluentDOM(self::XML)->find('//html/div');
    $this->assertTrue($doc->hasClass('test1') === TRUE);
    $this->assertTrue($doc->hasClass('unknown') === FALSE);
  }

  /**
  *
  * @group Attributes
  */
  function testRemoveClass() {
    $doc = FluentDOM(self::XML)->find('//html/div');
    $this->assertEquals('test1 test2', $doc[0]->getAttribute('class'));
    $this->assertEquals('test2', $doc[1]->getAttribute('class'));
    $doc->removeClass('test2');
    $this->assertEquals('test1', $doc[0]->getAttribute('class'));
    $this->assertTrue($doc[1]->hasAttribute('class') === FALSE);
  }

  /**
  *
  * @group Attributes
  */
  function testToggleClass() {
    $doc = FluentDOM(self::XML)->find('//html/div');
    $this->assertEquals('test1 test2', $doc[0]->getAttribute('class'));
    $this->assertEquals('test2', $doc[1]->getAttribute('class'));
    $doc->toggleClass('test1');
    $this->assertEquals('test2', $doc[0]->getAttribute('class'));
    $this->assertEquals('test2 test1', $doc[1]->getAttribute('class'));
  }


  /*
  * helper
  */

  /**
   *
   * @uses testAttrWriteCallback
   */
  function callbackForAttr($node, $index) {
    return $node->nodeName;
  }

  /**
   *
   * @uses testNotWithFunction()
   */
  function callbackTestNotWithFunction($node, $index) {
    return $node->nodeName != "items";
  }

  /**
   *
   * @uses testFilterWithFunction()
   */
  function callbackTestFilterWithFunction($node, $index) {
    return $node->nodeName == "items";
  }
}
?>
