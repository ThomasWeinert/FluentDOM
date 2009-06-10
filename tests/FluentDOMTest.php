<?php
require_once 'PHPUnit/Framework.php';
require_once '../FluentDOM.php';

/**
 * Test class for FluentDOM.
 */
class FluentDOMTest extends PHPUnit_Framework_TestCase {

  const XML = '
    <items>
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
  * Constructor
  */

  /**
  *
  * @group Constructors
  */
  function testConstructorWithString() {
    $doc = new FluentDOM(self::XML);
    $this->assertTrue($doc instanceof FluentDOM);
  }

  /**
  *
  * @group Constructors
  */
  function testConstructorWithFluentDOM() {
    $doc = new FluentDOM(self::XML);
    $doc = new FluentDOM($doc);
    $this->assertTrue($doc instanceof FluentDOM);
  }

  /**
  *
  * @group Constructors
  */
  function testConstructorWithDOMDocument() {
    $dom = new DOMDocument();
    $dom->loadXML(self::XML);
    $doc = new FluentDOM($dom);
    $this->assertTrue($doc instanceof FluentDOM);
  }

  /**
  *
  * @group Constructors
  */
  function testConstructorWithDomNode() {
    $dom = new DOMDocument();
    $dom->loadXML(self::XML);
    $doc = new FluentDOM($dom->documentElement);
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertEquals($dom->documentElement, $doc[0]);
  }

  /**
  *
  * @group Constructors
  */
  function testConstructorWithInvalidSource() {
    try {
      new FluentDOM(NULL);
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
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
  
  /*
  * DOMNodeList emulation
  */

  /**
  *
  */
  function testItem() {
    $doc = FluentDOM(self::XML);
    $doc = $doc->find('/items');
    $this->assertEquals($doc->document->documentElement, $doc->item(0));
    $this->assertEquals(NULL, $doc->item(-10));
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
  function testIs() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $this->assertTrue($doc->is('name() = "items"'));
    $this->assertFalse($doc->is('name() = "no-items"'));
  }

  /**
  *
  * @group TraversingFilter
  */
  function testMap() {
    $this->assertFileExists('data/map.src.xml');
    $dom = FluentDOM(file_get_contents('data/map.src.xml'));
    $dom->find('//p')
      ->append(
        implode(
          ', ',
          $dom
            ->find('//input')
            ->map(
              create_function('$node, $item', 'return FluentDOM($node)->attr("value");')
            )
        )
      );
    $this->assertTrue($dom instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/map.tgt.xml', $dom);
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
  function testSliceByRangeStartLtEnd() {
    $this->assertFileExists('data/sliceByRangeStartLtEnd.src.xml');
    $doc = FluentDOM(file_get_contents('data/sliceByRangeStartLtEnd.src.xml'))
      ->find('//p')
      ->slice(0,3)
      ->replaceAll('//div');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/sliceByRangeStartLtEnd.tgt.xml', $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceByRangeStartGtEnd() {
    $this->assertFileExists('data/sliceByRangeStartGtEnd.src.xml');
    $doc = FluentDOM(file_get_contents('data/sliceByRangeStartGtEnd.src.xml'))
      ->find('//p')
      ->slice(5,2)
      ->replaceAll('//div');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/sliceByRangeStartGtEnd.tgt.xml', $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceByNegRange() {
    $this->assertFileExists('data/sliceByNegRange.src.xml');
    $doc = FluentDOM(file_get_contents('data/sliceByNegRange.src.xml'))
      ->find('//p')
      ->slice(1,-2)
      ->replaceAll('//div');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/sliceByNegRange.tgt.xml', $doc);
  }

  /**
  *
  * @group TraversingFilter
  */
  function testSliceToEnd() {
    $this->assertFileExists('data/sliceToEnd.src.xml');
    $doc = FluentDOM(file_get_contents('data/sliceToEnd.src.xml'))
      ->find('//p')
      ->slice(3)
      ->replaceAll('//div');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/sliceToEnd.tgt.xml', $doc);
  }

  /*
  * Traversing - Finding
  */

  /**
  *
  * @group TraversingFind
  */
  function testAdd() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testChildren() {
    $this->markTestIncomplete('This test has not been implemented yet.');
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
  function testNextSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testNextAllSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testParent() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testParents() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testPrevSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testPrevAllSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group TraversingFind
  */
  function testSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
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
    $endDocRoot = $doc->end();
    $this->assertTrue($endDoc === $endDocRoot);
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
    $this->markTestIncomplete('This test has not been implemented yet.');
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
    $this->assertFileExists('data/append.src.xml');
    $doc = FluentDOM(file_get_contents('data/append.src.xml'))
      ->find('//p')
      ->append('<strong>Hello</strong>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/append.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testAppendTo() {
    $this->assertFileExists('data/appendTo.src.xml');
    $doc = FluentDOM(file_get_contents('data/appendTo.src.xml'))
      ->find('//span')
      ->appendTo('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/appendTo.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testPrepend() {
    $this->assertFileExists('data/prepend.src.xml');
    $doc = FluentDOM(file_get_contents('data/prepend.src.xml'))
      ->find('//p')
      ->prepend('<strong>Hello</strong>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/prepend.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testPrependTo() {
    $this->assertFileExists('data/prependTo.src.xml');
    $doc = FluentDOM(file_get_contents('data/prependTo.src.xml'))
      ->find('//span')
      ->prependTo('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/prependTo.tgt.xml', $doc);
  }

  /*
  * Manipulation - Inserting Outside
  */

  /**
  *
  * @group Manipulation
  */
  function testAfter() {
    $this->assertFileExists('data/after.src.xml');
    $doc = FluentDOM(file_get_contents('data/after.src.xml'))
      ->formatOutput()
      ->find('//p')
      ->after('<b>Hello</b>')
      ->after(' World');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/after.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testBefore() {
    $this->assertFileExists('data/before.src.xml');
    $doc = FluentDOM(file_get_contents('data/before.src.xml'))
      ->formatOutput()
      ->find('//p')
      ->before(' World')
      ->before('<b>Hello</b>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/before.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testInsertAfter() {
    $this->assertFileExists('data/insertAfter.src.xml');
    $doc = FluentDOM(file_get_contents('data/insertAfter.src.xml'))
      ->find('//p')
      ->insertAfter('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/insertAfter.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testInsertBefore() {
    $this->assertFileExists('data/insertBefore.src.xml');
    $doc = FluentDOM(file_get_contents('data/insertBefore.src.xml'))
      ->find('//p')
      ->insertBefore('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/insertBefore.tgt.xml', $doc);
  }

  /*
  * Manipulation - Inserting Around
  */

  /**
  *
  * @group Manipulation
  */
  function testWrap() {
    $this->assertFileExists('data/wrap.src.xml');
    $doc = FluentDOM(file_get_contents('data/wrap.src.xml'))
      ->find('//p')
      ->wrap('<div class="outer"><div class="inner"></div></div>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/wrap.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapAll() {
    $this->assertFileExists('data/wrapAll.src.xml');
    $doc = FluentDOM(file_get_contents('data/wrapAll.src.xml'))
      ->find('//p')
      ->wrapAll('<div class="wrapper"/>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/wrapAll.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testWrapInner() {
    $this->assertFileExists('data/wrapInner.src.xml');
    $doc = FluentDOM(file_get_contents('data/wrapInner.src.xml'))
      ->find('//p')
      ->wrapInner('<b></b>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/wrapInner.tgt.xml', $doc);
  }

  /*
  * Manipulation - Replacing
  */

  /**
  *
  * @group Manipulation
  */
  function testReplaceWith() {
    $this->assertFileExists('data/replaceWith.src.xml');
    $doc = FluentDOM(file_get_contents('data/replaceWith.src.xml'))
      ->find('//p')
      ->replaceWith('<b>Paragraph. </b>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/replaceWith.tgt.xml', $doc);
  }

  /**
  *
  * @group Manipulation
  */
  function testReplaceAll() {
    $this->assertFileExists('data/replaceAll.src.xml');
    $doc = FluentDOM(file_get_contents('data/replaceAll.src.xml'))
      ->node('<b id="sample">Paragraph. </b>')
      ->replaceAll('//p');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/replaceAll.tgt.xml', $doc);
  }

  /*
  * Manipulation - Removing
  */

  /**
  *
  * @group Manipulation
  */
  function testEmpty() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group Manipulation
  */
  function testRemove() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Attributes
  */

  /**
  *
  * @group Attributes
  */
  function testNode() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group Attributes
  */
  function testAttrRead() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group Attributes
  */
  function testAttrWrite() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group Attributes
  */
  function testRemoveAttr() {
    $this->markTestIncomplete('This test has not been implemented yet.');
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
}
?>
