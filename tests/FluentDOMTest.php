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
require_once 'FluentDomTestCase.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOM.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMTest extends FluentDomTestCase {

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

  function testSetLoadersInvalid() {
    try {
      $fd = new FluentDOM();
      $fd->setLoaders(array(new stdClass));
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /*
  * Properties
  */

  /**
  *
  * @group Properties
  */
  function testPropertyDocument() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertTrue(isset($fd->document));
    $this->assertTrue($fd->document instanceof DOMDocument);
    try {
      $fd->document = NULL;
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  *
  * @group Properties
  */
  function testPropertyXPath() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertTrue(isset($fd->xpath));
    $this->assertTrue($fd->xpath instanceof DOMXPath);
    try {
      $fd->xpath = NULL;
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  *
  * @group Properties
  */
  function testPropertyLength() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertTrue(isset($fd->length));
    $this->assertEquals(0, $fd->length);
    $fd = $fd->find('/items');
    $this->assertEquals(1, $fd->length);
    try {
      $fd->length = 50;
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  *
  * @group Properties
  */
  function testDynamicProperty() {
    $fd = $this->getFixtureFromString(self::XML);
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
  *
  * @group MagicFunctions
  */
  function testMagicToString() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertEquals($fd->document->saveXML(), (string)$fd);
  }

  /**
  *
  * @group MagicFunctions
  */
  function testMagicToStringHTML() {
    $dom = new DOMDocument();
    $dom->loadHTML('<html><body><br></body></html>');
    $loader = $this->getMock('FluentDOMLoader');
    $loader->expects($this->once())
           ->method('load')
           ->with($this->equalTo(''), $this->equalTo('html'))
           ->will($this->returnValue($dom));
    $fd = new FluentDOM();
    $fd->setLoaders(array($loader));
    $fd = $fd->load('', 'html');
    $this->assertEquals($dom->saveHTML(), (string)$fd);
  }

  /**
  *
  * @group MagicFunctions
  */
  function testMagicCallUnknown() {
    try {
      $fd = new FluentDOM();
      $fd->invalidDynamicMethodName();
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /*
  * Interfaces
  */

  /**
  *
  * @group Interfaces
  */
  function testInterfaceArrayAccessIsset() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $this->assertTrue($fd instanceof ArrayAccess);
    $this->assertEquals(TRUE, isset($fd[1]));
    $this->assertEquals(FALSE, isset($fd[200]));
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceArrayAccessGet() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $this->assertTrue($fd instanceof ArrayAccess);
    $this->assertEquals('item', $fd[1]->nodeName);
    $this->assertEquals(1, $fd[1]->getAttribute('index'));
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceArrayAccessSet() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $this->assertTrue($fd instanceof ArrayAccess);
    try {
      $fd[1] = NULL;
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceArrayAccessUnset() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $this->assertTrue($fd instanceof ArrayAccess);
    try {
      unset($fd[1]);
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceCountable() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertTrue($fd instanceof Countable);
    $this->assertEquals(0, count($fd));
    $fd = $fd->find('//item');
    $this->assertEquals(3, count($fd));
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceIteratorMethods() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $this->assertTrue($fd instanceof Iterator);
    $this->assertEquals(0, $fd->current()->getAttribute('index'));
    $fd->next();
    $this->assertEquals(1, $fd->current()->getAttribute('index'));
    $this->assertEquals(1, $fd->key());
    $fd->rewind();
    $this->assertEquals(0, $fd->current()->getAttribute('index'));
    $this->assertEquals(0, $fd->key());
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceIteratorLoop() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $this->assertTrue($fd instanceof Iterator);
    $counter = 0;
    foreach ($fd as $item) {
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
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $this->assertTrue($fd instanceof SeekableIterator);
    $this->assertEquals(0, $fd->key());
    $fd->seek(2);
    $this->assertEquals(2, $fd->key());
    try {
      $fd->seek(200);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  *
  * @group Interfaces
  */
  function testInterfaceRecursiveIterator() {
    $iterator = new RecursiveIteratorIterator(
      $this->getFixtureFromString(self::XML)->find('/*'),
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
    $fd = $this->getFixtureFromString(self::XML)->find('/items');
    $this->assertEquals($fd->document->documentElement, $fd->item(0));
    $this->assertEquals(NULL, $fd->item(-10));
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
      $fd = $this->getFixtureFromString(self::XML)
        ->find('//body//*')
        ->each('invalidCallbackFunctionName');
      $this->fail('An expected exception has not been raised.');
    } catch (BadFunctionCallException $expected) {
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
    $fd = $this->getFixtureFromString(self::XML);
    $nodes = $fd->node($fd->document->createElement('div'));
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertEquals(1, count($nodes));
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithDOMText() {
    $fd = $this->getFixtureFromString(self::XML);
    $nodes = $fd->node($fd->document->createTextNode('div'));
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertEquals(1, count($nodes));
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithInvalidContent() {
    try {
      $fd = $this->getFixtureFromString(self::XML)
        ->node(NULL);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithEmptyContent() {
    try {
      $fd = $this->getFixtureFromString(self::XML)
        ->node('');
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  *
  * @group CoreFunctions
  */
  function testNodeWithEmptyList() {
    try {
      $fd = $this->getFixtureFromString(self::XML);
      $fd->node(
          $fd->find('UnknownTagName')
        );
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
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
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $eqFd = $fd->eq(0);
    $this->assertEquals(1, $eqFd->length);
    $this->assertTrue($eqFd !== $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testFilter() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $filterFd = $fd->filter('name() = "items"');
    $this->assertEquals(1, $filterFd->length);
    $this->assertTrue($filterFd !== $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testFilterWithFunction() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $filterFd = $fd->filter(array($this, 'callbackTestFilterWithFunction'));
    $this->assertEquals(1, $filterFd->length);
    $this->assertTrue($filterFd !== $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testIs() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $this->assertTrue($fd->is('name() = "items"'));
    $this->assertFalse($fd->is('name() = "invalidItemName"'));
  }

  /**
  *
  * @group TraversingFilter
  */
  function testIsOnEmptyList() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertTrue($fd->length == 0);
    $this->assertFalse($fd->is('name() = "items"'));
  }

  /**
  *
  * @group TraversingFilter
  */
  function testMap() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->append(
        implode(
          ', ',
          $fd
            ->find('//input')
            ->map(
              create_function('$node, $index', 'return FluentDOM($node)->attr("value");')
            )
        )
      );
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testMapMixedResult() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->append(
        implode(
          ', ',
          $fd
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
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testMapInvalidCallback() {
    $fd = $this->getFixtureFromFile('testMap');
    try {
      $fd->find('//p')
        ->map('invalidCallbackFunctionName');
        $this->fail('An expected exception has not been raised.');
    } catch (BadFunctionCallException $expected) {
    }
  }

  /**
  *
  * @group TraversingFilter
  */
  function testNot() {
    $fd = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $notDoc = $fd->not('name() != "items"');
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testNotWithFunction() {
    $fd = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $notDoc = $fd->not(array($this, 'callbackTestNotWithFunction'));
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceByRangeStartLtEnd() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->slice(0,3)
      ->replaceAll('//div');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceByRangeStartGtEnd() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->slice(5,2)
      ->replaceAll('//div');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceByNegRange() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->slice(1,-2)
      ->replaceAll('//div');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceToEnd() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->slice(3)
      ->replaceAll('//div');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
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
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd->find('//p')
       ->append('<strong>Hello</strong>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
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
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $items = $fd->find('//item');
    $this->assertTrue($fd instanceof FluentDOM);
    $doc = $fd
      ->find('//html/div')
      ->append($items);
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testAppendTo() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//span')
      ->appendTo('//div[@id = "foo"]');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testPrepend() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->prepend('<strong>Hello</strong>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testPrependTo() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//span')
      ->prependTo('//div[@id = "foo"]');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Inserting Outside
  */

  /**
  *
  * @group Manipulation
  */
  function testAfter() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->formatOutput()
      ->find('//p')
      ->after('<b>Hello</b>')
      ->after(' World');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testBefore() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->formatOutput()
      ->find('//p')
      ->before(' World')
      ->before('<b>Hello</b>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testInsertAfter() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->insertAfter('//div[@id = "foo"]');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testInsertBefore() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->insertBefore('//div[@id = "foo"]');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Inserting Around
  */

  /**
  *
  * @group Manipulation
  */
  function testWrap() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->wrap('<div class="outer"><div class="inner"></div></div>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapWithDOMElement() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $dom = $fd->document;
    $div = $dom->createElement('div');
    $div->setAttribute('class', 'wrapper');
    $fd->find('//p')->wrap($div);
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapWithDOMNodeList() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $divs = $fd->xpath->query('//div[@class = "wrapper"]');
    $this->assertTrue($fd instanceof FluentDOM);
    $fd->find('//p')->wrap($divs);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
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
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $dom = $fd->document;
    $divs[0] = $dom->createElement('div');
    $divs[0]->setAttribute('class', 'wrapper');
    $divs[1] = $dom->createElement('div');
    $fd->find('//p')->wrap($divs);
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapAllSingle() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->wrapAll('<div class="wrapper"/>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapAllComplex() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->wrapAll('<div class="wrapper"><div>INNER</div></div>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapInner() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->wrapInner('<b></b>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Replacing
  */

  /**
  *
  * @group Manipulation
  */
  function testReplaceWith() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->replaceWith('<b>Paragraph. </b>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testReplaceAll() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->node('<b id="sample">Paragraph. </b>')
      ->replaceAll('//p');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testReplaceAllWithNode() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->node('<b id="sample">Paragraph. </b>')
      ->replaceAll(
        $fd->find('//p')->item(1)
      );
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
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
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p[@class = "first"]')
      ->empty();
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  *
  * @group Manipulation
  */
  function testRemove() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p[@class = "first"]')
      ->remove();
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Copying
  */

  /**
  *
  * @group Manipulation
  */
  function testClone() {
    $fd = FluentDOM(self::XML)->find('//item');
    $clonedNodes = $fd->clone();
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertTrue($clonedNodes instanceof FluentDOM);
    $this->assertTrue($fd[0] !== $clonedNodes[0]);
    $this->assertEquals($fd[0]->nodeName, $clonedNodes[0]->nodeName);
    $this->assertEquals($fd[1]->getAttribute('index'), $clonedNodes[1]->getAttribute('index'));
    $this->assertEquals(count($fd), count($clonedNodes));
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
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->removeAttr('index');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Attributes - Classes
  */

  /**
  *
  * @group Attributes
  */
  function testAddClass() {
    $fd = FluentDOM(self::XML)->find('//html/div');
    $this->assertTrue($fd->hasClass('added') === FALSE);
    $fd->addClass('added');
    $this->assertTrue($fd->hasClass('added') === TRUE);
  }

  /**
  *
  * @group Attributes
  */
  function testHasClass() {
    $fd = FluentDOM(self::XML)->find('//html/div');
    $this->assertTrue($fd->hasClass('test1') === TRUE);
    $this->assertTrue($fd->hasClass('unknown') === FALSE);
  }

  /**
  *
  * @group Attributes
  */
  function testRemoveClass() {
    $fd = FluentDOM(self::XML)->find('//html/div');
    $this->assertEquals('test1 test2', $fd[0]->getAttribute('class'));
    $this->assertEquals('test2', $fd[1]->getAttribute('class'));
    $fd->removeClass('test2');
    $this->assertEquals('test1', $fd[0]->getAttribute('class'));
    $this->assertTrue($fd[1]->hasAttribute('class') === FALSE);
  }

  /**
  *
  * @group Attributes
  */
  function testToggleClass() {
    $fd = FluentDOM(self::XML)->find('//html/div');
    $this->assertEquals('test1 test2', $fd[0]->getAttribute('class'));
    $this->assertEquals('test2', $fd[1]->getAttribute('class'));
    $fd->toggleClass('test1');
    $this->assertEquals('test2', $fd[0]->getAttribute('class'));
    $this->assertEquals('test2 test1', $fd[1]->getAttribute('class'));
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
