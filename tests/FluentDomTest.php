<?php
require_once 'PHPUnit/Framework.php';
require_once '../FluentDom.php';

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
    </items>
  ';
  
  /**
  * Constructor
  */
  
  function testConstructorWithString() {
    $doc = new FluentDOM(self::XML);
    $this->assertTrue($doc instanceof FluentDOM);
  }
  
  function testConstructorWithFluentDOM() {
    $doc = new FluentDOM(self::XML);
    $doc = new FluentDOM($doc);
    $this->assertTrue($doc instanceof FluentDOM);
  }
  
  function testConstructorWithDOMDocument() {
    $dom = new DOMDocument();
    $dom->loadXML(self::XML);
    $doc = new FluentDOM($dom);
    $this->assertTrue($doc instanceof FluentDOM);
  }
  
  function testConstructorWithDomNode() {
    $dom = new DOMDocument();
    $dom->loadXML(self::XML);
    $doc = new FluentDOM($dom->documentElement);
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertEquals($dom->documentElement, $doc[0]);
  }
  
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
  
  function testPropertyDocument() {
    $doc = FluentDOM(self::XML);
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
  
  function testPropertyXPath() {
    $doc = FluentDOM(self::XML);
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
  
  function testPropertyLength() {
    $doc = FluentDOM(self::XML);
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

  /*
  * __toString() method
  */

  function testMagicToString() {
    $doc = FluentDOM(self::XML);
    $this->assertEquals($doc->document->saveXML(), (string)$doc);
  }
  
  /*
  * DOMNodeList emulation
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
  
  function testEq() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $eqDoc = $doc->eq(0);
    $this->assertEquals(1, $eqDoc->length);
    $this->assertTrue($eqDoc !== $doc);
  }
  
  function testFilter() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $filterDoc = $doc->filter('name() = "items"');
    $this->assertEquals(1, $filterDoc->length);
    $this->assertTrue($filterDoc !== $doc);
  }
  
  function testIs() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $this->assertTrue($doc->is('name() = "items"'));
    $this->assertFalse($doc->is('name() = "no-items"'));
  }
  
  function testMap() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testNot() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $notDoc = $doc->not('name() != "items"');
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $doc);
  }
  
  function testSlice() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Traversing - Finding
  */
  
  function testAdd() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testChildren() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testFind() {
    $doc = FluentDOM(self::XML)->find('/*');
    $this->assertEquals(1, $doc->length);
    $findDoc = $doc->find('group/item');
    $this->assertEquals(3, $findDoc->length);
    $this->assertTrue($findDoc !== $doc);
  }
  
  function testNextSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testNextAllSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testParent() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  function testParents() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testPrevSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testPrevAllSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Traversing - Chaining
  */
  
  function testAndSelf() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testEnd() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testXMLWrite() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testXMLRead() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testTextWrite() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testTextRead() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Manipulation - Inserting Inside
  */
  
  function testAppend() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testAppendTo() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testPrepend() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testPrependTo() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Manipulation - Inserting Outside
  */
  
  function testAfter() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testBefore() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testInsertAfter() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testInsertBefore() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  /*
  * Manipulation - Inserting Around
  */
  
  function testWrap() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testWrapAll() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testWrapInner() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  /*
  * Manipulation - Replacing
  */
  
  function testReplaceWith() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testReplaceAll() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  /*
  * Manipulation - Removing
  */
  
  function testEmpty() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testRemove() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Attributes
  */
  
  function testNode() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testAttrRead() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testAttrWrite() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testRemoveAttr() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Attributes - Classes
  */
  
  function testAddClass() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testHasClass() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testRemoveClass() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
  
  function testToggleClass() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
}
?>