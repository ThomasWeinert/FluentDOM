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
  * @group constructors
  */
  function testConstructorWithString() {
    $doc = new FluentDOM(self::XML);
    $this->assertTrue($doc instanceof FluentDOM);
  }

  /**
  *
  * @group constructors
  */
  function testConstructorWithFluentDOM() {
    $doc = new FluentDOM(self::XML);
    $doc = new FluentDOM($doc);
    $this->assertTrue($doc instanceof FluentDOM);
  }

  /**
  *
  * @group constructors
  */
  function testConstructorWithDOMDocument() {
    $dom = new DOMDocument();
    $dom->loadXML(self::XML);
    $doc = new FluentDOM($dom);
    $this->assertTrue($doc instanceof FluentDOM);
  }

  /**
  *
  * @group constructors
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
  * @group constructors
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
  * @group properties
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
  * @group properties
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
  * @group properties
  * @covers FluentDOM::find
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

  /*
  * __toString() method
  */

  /**
  *
  * @group magicFunctions
  */
  function testMagicToString() {
    $doc = FluentDOM(self::XML);
    $this->assertEquals($doc->document->saveXML(), (string)$doc);
  }

  /*
  * DOMNodeList emulation
  */

  /**
  *
  * @covers FluentDOM::find
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
  * @group traversingFilter
  * @covers FluentDOM::find
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
  * @group traversingFilter
  * @covers FluentDOM::find
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
  * @group traversingFilter
  * @covers FluentDOM::find
  */
  function testIs() {
    $doc = FluentDOM(self::XML)->find('//*');
    $this->assertTrue($doc->length > 1);
    $this->assertTrue($doc->is('name() = "items"'));
    $this->assertFalse($doc->is('name() = "no-items"'));
  }

  /**
  *
  * @group traversingFilter
  */
  function testMap() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFilter
  * @covers FluentDOM::find
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
  * @group traversingFilter
  */
  function testSlice() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Traversing - Finding
  */

  /**
  *
  * @group traversingFind
  */
  function testAdd() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFind
  */
  function testChildren() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFind
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
  * @group traversingFind
  */
  function testNextSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFind
  */
  function testNextAllSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFind
  */
  function testParent() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFind
  */
  function testParents() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFind
  */
  function testPrevSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFind
  */
  function testPrevAllSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingFind
  */
  function testSiblings() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Traversing - Chaining
  */

  /**
  *
  * @group traversingChain
  * @covers FluentDOM::find
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
  * @group traversingChain
  * @covers FluentDOM::find
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
  * @group traversingChain
  * @covers FluentDOM::find
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
  * @group traversingChain
  */
  function testXMLWrite() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group traversingChain
  * @covers FluentDOM::find
  */
  function testTextRead() {
    $expect = 'text1text2text3';
    $text = FluentDOM(self::XML)->find('//group')->text();
    $this->assertEquals($expect, $text);
  }

  /**
  *
  * @group traversingChain
  * @covers FluentDOM::find
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
  * @group manipulation
  */
  function testAppend() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group manipulation
  */
  function testAppendTo() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group manipulation
  */
  function testPrepend() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group manipulation
  */
  function testPrependTo() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Manipulation - Inserting Outside
  */

  /**
  *
  * @group manipulation
  * @covers FluentDOM::find
  */
  function testAfter() {
    $doc = FluentDOM(file_get_contents('data/after.src.xml'))
      ->find('//p')
      ->after('<b>Hello</b>')
      ->after(' World');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/after.tgt.xml', $doc);
  }

  /**
  *
  * @group manipulation
  * @covers FluentDOM::find
  */
  function testBefore() {
    $doc = FluentDOM(file_get_contents('data/before.src.xml'))
      ->find('//p')
      ->before(' World')
      ->before('<b>Hello</b>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/before.tgt.xml', $doc);
  }

  /**
  *
  * @group manipulation
  * @covers FluentDOM::find
  */
  function testInsertAfter() {
    $doc = FluentDOM(file_get_contents('data/insertAfter.src.xml'))
      ->find('//p')
      ->insertAfter('//div[@id = "foo"]');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/insertAfter.tgt.xml', $doc);
  }

  /**
  *
  * @group manipulation
  * @covers FluentDOM::find
  */
  function testInsertBefore() {
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
  * @group manipulation
  * @covers FluentDOM::find
  */
  function testWrap() {
    $doc = FluentDOM(file_get_contents('data/wrap.src.xml'))
      ->find('//p')
      ->wrap('<div class="outer"><div class="inner"></div></div>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/wrap.tgt.xml', $doc);
  }

  /**
  *
  * @group manipulation
  * @covers FluentDOM::find
  */
  function testWrapAll() {
    $doc = FluentDOM(file_get_contents('data/wrapAll.src.xml'))
      ->find('//p')
      ->wrapAll('<div class="wrapper"/>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXMLFile('data/wrapAll.tgt.xml', $doc);
  }

  /**
  *
  * @group manipulation
  * @covers FluentDOM::find
  */
  function testWrapInner() {
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
  * @group manipulation
  */
  function testReplaceWith() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group manipulation
  */
  function testReplaceAll() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Manipulation - Removing
  */

  /**
  *
  * @group manipulation
  */
  function testEmpty() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group manipulation
  */
  function testRemove() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Attributes
  */

  /**
  *
  * @group attributes
  */
  function testNode() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group attributes
  */
  function testAttrRead() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group attributes
  */
  function testAttrWrite() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
  *
  * @group attributes
  */
  function testRemoveAttr() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /*
  * Attributes - Classes
  */

  /**
  *
  * @group attributes
  */
  function testAddClass() {
    $doc = FluentDOM(self::XML)->find('//html/div');
    $this->assertTrue($doc->hasClass('added') === FALSE);
    $doc->addClass('added');
    $this->assertTrue($doc->hasClass('added') === TRUE);
  }

  /**
  *
  * @group attributes
  */
  function testHasClass() {
    $doc = FluentDOM(self::XML)->find('//html/div');
    $this->assertTrue($doc->hasClass('test1') === TRUE);
    $this->assertTrue($doc->hasClass('unknown') === FALSE);
  }

  /**
  *
  * @group attributes
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
  * @group attributes
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