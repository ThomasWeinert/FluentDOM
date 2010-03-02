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
require_once (dirname(__FILE__).'/FluentDOMTestCase.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOM.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMTest extends FluentDOMTestCase {

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
  * @group Functions
  */
  public function testFunction() {
    $fd = FluentDOM();
    $this->assertTrue($fd instanceof FluentDOM);
  }

  /**
  * @group Functions
  */
  public function testFunctionWithContent() {
    $dom = new DOMDocument();
    $node = $dom->appendChild($dom->createElement('html'));
    $fd = FluentDOM($node);
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertTag(
      array('tag' => 'html'),
      $fd->document
    );
  }

  /*
  * Core functions
  */
  public function testMagicMethodCallWithInvalidMethodName() {
    $fd = new FluentDOM();
    try {
      $fd->InvalidMethodName();
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  */
  public function testEach() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//body//*')
      ->each(
        create_function(
          '$node, $item',
          '$fd = new FluentDOM();
           $fd->load($node);
           $fd->prepend("EACH > ");
          ')
      );
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group CoreFunctions
  */
  public function testEachWithInvalidFunction() {
    try {
      $this->getFixtureFromString(self::XML)
        ->find('//body//*')
        ->each('invalidCallbackFunctionName');
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  */
  public function testNode() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fdItems = $this->getFixtureFromString(
      '<samples><b id="first">Paragraph. </b></samples>'
    );
    $fd->node(
        $fdItems
          ->find('//b[@id = "first"]')
          ->removeAttr('id')
          ->addClass('imported')
      )
      ->replaceAll('//p');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group CoreFunctions
  */
  public function testNodeWithNameAndAttributes() {
    $fd = new FluentDOM();
    $doc = $fd->append(
      $fd->node(
        '<sample/>', array('attribute' => 'yes')
      )
    );
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertXmlStringEqualsXmlString(
      '<?xml version="1.0"?>'."\n".'<sample attribute="yes"/>',
      $doc->document->saveXML()
    );
  }

  /**
  * @group CoreFunctions
  */
  public function testNodeWithDomelement() {
    $fd = $this->getFixtureFromString(self::XML);
    $nodes = $fd->node($fd->document->createElement('div'));
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertEquals(1, count($nodes));
  }

  /**
  * @group CoreFunctions
  */
  public function testNodeWithDomtext() {
    $fd = $this->getFixtureFromString(self::XML);
    $nodes = $fd->node($fd->document->createTextNode('div'));
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertEquals(1, count($nodes));
  }

  /**
  * @group CoreFunctions
  */
  public function testNodeWithInvalidContent() {
    try {
      $fd = $this->getFixtureFromString(self::XML)
        ->node(NULL);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  */
  public function testNodeWithEmptyContent() {
    try {
      $fd = $this->getFixtureFromString(self::XML)
        ->node('');
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  * @group CoreFunctions
  */
  public function testNodeWithEmptyList() {
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
  * @group CoreFunctions
  */
  public function testToArray() {
    $fd = $this->getFixtureFromString(self::XML)->find('/items/*');
    $this->assertSame(
      array(
        $fd[0],
        $fd[1]
      ),
      $fd->toArray()
    );
  }

  /*
  * Traversing - Filtering
  */

  /**
  * @group TraversingFilter
  */
  public function testEq() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $eqFd = $fd->eq(0);
    $this->assertAttributeSame(
      array(
        $fd[0]
      ),
      '_array',
      $eqFd
    );
    $this->assertTrue($eqFd !== $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testEqWithNegativeOffset() {
    $fd = $this->getFixtureFromString(self::XML)->find('/items/*');
    $eqFd = $fd->eq(-2);
    $this->assertAttributeSame(
      array(
        $fd[0]
      ),
      '_array',
      $eqFd
    );
    $this->assertTrue($eqFd !== $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testFilter() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $filterFd = $fd->filter('name() = "items"');
    $this->assertEquals(1, $filterFd->length);
    $this->assertTrue($filterFd !== $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testGet() {
    $fd = $this->getFixtureFromString(self::XML)->find('/items/*');
    $this->assertSame(
      array(
        $fd[0],
        $fd[1]
      ),
      $fd->get()
    );
  }

  /**
  * @group TraversingFilter
  */
  public function testGetWithPosition() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertSame(
      array(
        $fd[0]
      ),
      $fd->get(0)
    );
  }

  /**
  * @group TraversingFilter
  */
  public function testGetWithNegativePosition() {
    $fd = $this->getFixtureFromString(self::XML)->find('/items/*');
    $this->assertSame(
      array(
        $fd[0]
      ),
      $fd->get(-2)
    );
  }

  /**
  * @group TraversingFilter
  */
  public function testGetWithInvalidPosition() {
    $fd = $this->getFixtureFromString(self::XML)->find('/*');
    $this->assertSame(
      array(),
      $fd->get(99)
    );
  }

  /**
  * @group TraversingFilter
  */
  public function testFilterWithFunction() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $filterFd = $fd->filter(array($this, 'callbackTestFilterWithFunction'));
    $this->assertEquals(1, $filterFd->length);
    $this->assertTrue($filterFd !== $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testIs() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $this->assertTrue($fd->is('name() = "items"'));
    $this->assertFalse($fd->is('name() = "invalidItemName"'));
  }

  /**
  * @group TraversingFilter
  */
  public function testIsOnEmptyList() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertTrue($fd->length == 0);
    $this->assertFalse($fd->is('name() = "items"'));
  }

  /**
  * @group TraversingFilter
  */
  public function testMap() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->append(
        implode(
          ', ',
          $fd
            ->find('//input')
            ->map(
              create_function(
                '$node, $index',
                '$fd = new FluentDOM();
                 return $fd->load($node)->attr("value");'
              )
            )
        )
      );
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testMapMixedResult() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
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
  * @group TraversingFilter
  */
  public function testMapInvalidCallback() {
    $fd = $this->getFixtureFromFile('testMap');
    try {
      $fd
        ->find('//p')
        ->map('invalidCallbackFunctionName');
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group TraversingFilter
  */
  public function testNot() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $notDoc = $fd->not('name() != "items"');
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testNotWithFunction() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $notDoc = $fd->not(array($this, 'callbackTestNotWithFunction'));
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testSliceByRangeStartLtEnd() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->slice(0, 3)
      ->replaceAll('//div');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testSliceByRangeStartGtEnd() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->slice(5, 2)
      ->replaceAll('//div');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testSliceByNegRange() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->slice(1, -2)
      ->replaceAll('//div');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFilter
  */
  public function testSliceToEnd() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->slice(3)
      ->replaceAll('//div');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Traversing - Finding
  */

  /**
  * @group TraversingFind
  */
  public function testAddElements() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->add(
        $fd->find('//div')
      )
      ->toggleClass('inB');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testAddFromExpression() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->add('//div')
      ->toggleClass('inB');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testAddInContext() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->add('//p/b')
      ->toggleClass('inB');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testAddInvalidForeignNodes() {
    $fd = $this->getFixtureFromString(self::XML);
    $itemsFd = $this->getFixtureFromString(self::XML)->find('//item');
    try {
      $fd
        ->find('/items')
        ->add($itemsFd);
      $this->fail('An expected exception has not been raised.');
        $this->fail('An expected exception has not been raised.');
    } catch (OutOfBoundsException $expected) {
    }
  }

  /**
  * @group TraversingFind
  */
  public function testAddInvalidForeignNode() {
    $fd = $this->getFixtureFromString(self::XML);
    $itemsFd = $this->getFixtureFromString(self::XML)->find('//item');
    try {
      $fd
        ->find('/items')
        ->add($itemsFd[0]);
      $this->fail('An expected exception has not been raised.');
        $this->fail('An expected exception has not been raised.');
    } catch (OutOfBoundsException $expected) {
    }
  }

  /**
  * @group TraversingFind
  */
  public function testChildren() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//div[@id = "container"]/p')
      ->children()
      ->toggleClass('child');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testChildrenExpression() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//div[@id = "container"]/p')
      ->children('name() = "em"')
      ->toggleClass('child');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testFind() {
    $fd = $this->getFixtureFromString(self::XML)->find('/*');
    $this->assertEquals(1, $fd->length);
    $findFd = $fd->find('group/item');
    $this->assertEquals(3, $findFd->length);
    $this->assertTrue($findFd !== $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testFindFromRootNode() {
    $fd = $this->getFixtureFromString(self::XML)->find('/*');
    $this->assertEquals(1, $fd->length);
    $findFd = $this->getFixtureFromString(self::XML)->find('/items');
    $this->assertEquals(1, $findFd->length);
    $this->assertTrue($findFd !== $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testFindWithNamespaces() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $doc = $fd ->find('//_:entry');
    $this->assertEquals(25, $doc->length);
    $value = $fd ->find('//openSearch:totalResults')->text();
    $this->assertEquals(38, $value);
  }

  /**
  * @group TraversingFind
  */
  public function testNext() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//button[@disabled]')
      ->next()
      ->text('This button is disabled.');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testNextAll() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//div[position() = 1]')
      ->nextAll()
      ->addClass('after');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testParent() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//body//*')
      ->each(
        create_function(
          '$node, $item',
          '$fd = new FluentDOM();
           $fd->load($node);
           $fd->prepend(
             $fd->document->createTextNode(
               $fd->parent()->item(0)->tagName." > "
             )
            );
          ')
      );
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testParents() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $this->assertTrue($fd instanceof FluentDOM);
    $parents = $fd
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
    $doc = $fd
      ->find('//b')
      ->append('<strong>'.htmlspecialchars($parents).'</strong>');
    $this->assertTrue($doc instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $doc);
  }

  /**
  * @group TraversingFind
  */
  public function testPrev() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//div[@id = "start"]')
      ->prev()
      ->addClass('before');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testPrevExpression() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//div[@class = "here"]')
      ->prev()
      ->addClass('nextTest');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testPrevAll() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//div[@id = "start"]')
      ->prev()
      ->addClass('before');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testPrevAllExpression() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//div[@class= "here"]')
      ->prevAll('.//span')
      ->addClass('nextTest');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testSiblings() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//li[@class = "hilite"]')
      ->siblings()
      ->addClass('before');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingFind
  */
  public function testClosest() {
    $attribute = $this->getFixtureFromString(self::XML)
      ->find('//item')
      ->closest('name() = "group"')
      ->attr("id");
    $this->assertEquals('1st', $attribute);
  }

  /**
  * @group TraversingFind
  */
  public function testClosestIsCurrentNode() {
    $attribute = $this->getFixtureFromString(self::XML)
      ->find('//item')
      ->closest('self::item[@index = "1"]')
      ->attr("index");
    $this->assertEquals('1', $attribute);
  }

  /*
  * Traversing - Chaining
  */

  /**
  * @group TraversingChain
  */
  public function testAndSelf() {
    $fd = $this->getFixtureFromString(self::XML)->find('/items')->find('.//item');
    $this->assertEquals(3, $fd->length);
    $andSelfFd = $fd->andSelf();
    $this->assertEquals(4, $andSelfFd->length);
    $this->assertTrue($andSelfFd !== $fd);
  }

  /**
  * @group TraversingChain
  */
  public function testEnd() {
    $fd = $this->getFixtureFromString(self::XML)->find('/items')->find('.//item');
    $this->assertEquals(3, $fd->length);
    $endFd = $fd->end();
    $this->assertEquals(1, $endFd->length);
    $this->assertTrue($endFd !== $fd);
    $endFdRoot = $endFd->end();
    $this->assertTrue($endFd !== $endFdRoot);
    $endFdRoot2 = $endFdRoot->end();
    $this->assertTrue($endFdRoot === $endFdRoot2);
  }

  /**
  * @group TraversingChain
  */
  public function testXmlRead() {
    $expect = '<item index="0">text1</item>'.
      '<item index="1">text2</item>'.
      '<item index="2">text3</item>';
    $xml = $this->getFixtureFromString(self::XML)->find('//group')->xml();
    $this->assertEquals($expect, $xml);
  }

  /**
  * @group TraversingChain
  */
  public function testXmlReadEmpty() {
    $xml = $this->getFixtureFromString('<items/>')->find('/items/*')->xml();
    $this->assertEquals('', $xml);
  }

  /**
  * @group TraversingChain
  */
  public function testXmlWrite() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p[position() = last()]')
      ->xml('<b>New</b>World');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingChain
  */
  public function testXmlWriteEmpty() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->xml('');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingChain
  */
  public function testXmlWriteWithCallback() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->xml(array($this, 'callbackForXml'));
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group TraversingChain
  */
  public function testTextRead() {
    $expect = 'text1text2text3';
    $text = $this->getFixtureFromString(self::XML)->formatOutput()->find('//group')->text();
    $this->assertEquals($expect, $text);
  }

  /**
  * @group TraversingChain
  */
  public function testTextWrite() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $textFd = $fd->text('changed');
    $this->assertEquals('changed', $fd[0]->textContent);
    $this->assertEquals('changed', $fd[1]->textContent);
    $this->assertTrue($fd === $textFd);
  }

  /**
  * @group TraversingChain
  */
  public function testTextWriteWithCallback() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $textFd = $fd->text(array($this, 'callbackForText'));
    $this->assertEquals('Callback #0: text1', $fd[0]->textContent);
    $this->assertEquals('Callback #1: text2', $fd[1]->textContent);
    $this->assertTrue($fd === $textFd);
  }

  /*
  * Manipulation - Inserting Inside
  */

  /**
  * @group Manipulation
  */
  public function testAppend() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->append('<strong>Hello</strong>');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testAppendDomelement() {
    $fd = new FluentDOM();
    $fd->append('<strong>Hello</strong>');
    $this->assertEquals('strong', $fd->find('/strong')->item(0)->nodeName);
  }

  /**
  * @group Manipulation
  */
  public function testAppendDomnodelist() {
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
  * @group Manipulation
  */
  public function testAppendWithCallback() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $doc = $fd
      ->find('//p')
      ->append(array($this, 'callbackForAppend'));
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testAppendOnEmptyDocumentWithCallback() {
    $fd = new FluentDOM;
    $doc = $fd->append(array($this, 'callbackForAppendNode'));
    $this->assertXmlStringEqualsXmlString(
      '<?xml version="1.0"?>'."\n".'<sample>Hello World</sample>',
      $doc->document->saveXML()
    );
  }

  /**
  * @group Manipulation
  */
  public function testAppendTo() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//span')
      ->appendTo('//div[@id = "foo"]');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testPrepend() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->find('//p')
      ->prepend('<strong>Hello</strong>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testPrependTo() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//span')
      ->prependTo('//div[@id = "foo"]');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Inserting Outside
  */

  /**
  * @group Manipulation
  */
  public function testAfter() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->formatOutput()
      ->find('//p')
      ->after('<b>Hello</b>')
      ->after(' World');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testBefore() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->formatOutput()
      ->find('//p')
      ->before(' World')
      ->before('<b>Hello</b>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testInsertAfter() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->insertAfter('//div[@id = "foo"]');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testInsertBefore() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->insertBefore('//div[@id = "foo"]');
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Inserting Around
  */

  /**
  * @group Manipulation
  */
  public function testWrap() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->wrap('<div class="outer"><div class="inner"></div></div>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testWrapWithDomelement() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $dom = $fd->document;
    $div = $dom->createElement('div');
    $div->setAttribute('class', 'wrapper');
    $fd->find('//p')->wrap($div);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testWrapWithDomnodelist() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $divs = $fd->xpath->query('//div[@class = "wrapper"]');
    $fd->find('//p')->wrap($divs);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testWrapWithInvalidArgument() {
    try {
      $this->getFixtureFromString(self::XML)
        ->find('//item')
        ->wrap(NULL);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group Manipulation
  */
  public function testWrapWithArray() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $dom = $fd->document;
    $divs[0] = $dom->createElement('div');
    $divs[0]->setAttribute('class', 'wrapper');
    $divs[1] = $dom->createElement('div');
    $fd->find('//p')->wrap($divs);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testWrapAllSingle() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->wrapAll('<div class="wrapper"/>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testWrapAllComplex() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->wrapAll('<div class="wrapper"><div>INNER</div></div>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testWrapInner() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->wrapInner('<b></b>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Replacing
  */

  /**
  * @group Manipulation
  */
  public function testReplaceWith() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->replaceWith('<b>Paragraph. </b>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testReplaceAll() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->node('<b id="sample">Paragraph. </b>')
      ->replaceAll('//p');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testReplaceAllWithNode() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->node('<b id="sample">Paragraph. </b>')
      ->replaceAll(
        $fd->find('//p')->item(1)
      );
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testReplaceAllWithInvalidArgument() {
    try {
      $this->getFixtureFromString(self::XML)
        ->node('<b id="sample">Paragraph. </b>')
        ->replaceAll(
          NULL
        );
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /*
  * Manipulation - Removing
  */

  /**
  * @group Manipulation
  */
  public function testEmpty() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p[@class = "first"]')
      ->empty();
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testRemove() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p[@class = "first"]')
      ->remove();
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  */
  public function testRemoveWithExpression() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->remove('@class = "first"');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Copying
  */

  /**
  * @group Manipulation
  */
  public function testClone() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $clonedNodes = $fd->clone();
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
  * @group Attributes
  */
  public function testAttrRead() {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//group/item')
      ->attr('index');
    $this->assertEquals('0', $fd);
  }
  /**
  * @group Attributes
  */
  public function testAttrReadFromRoot() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertEquals('1.0', $fd->find('/*')->attr('version'));
    $this->assertEquals('1.0', $fd->find('/items')->attr('version'));
    $this->assertEquals('1.0', $fd->find('//items')->attr('version'));
  }

  /**
  * @group Attributes
  */
  public function testAttrReadInvalid() {
    try {
      $this->getFixtureFromString(self::XML)
        ->find('//item')
        ->attr('');
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  /**
  * @group Attributes
  */
  public function testAttrReadNoMatch() {
    $fd = $this->getFixtureFromString(self::XML)->attr('index');
    $this->assertTrue(empty($fd));
  }

  /**
  * @group Attributes
  */
  public function testAttrReadOnDomtext() {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//item/text()')
      ->attr('index');
    $this->assertTrue(empty($fd));
  }

  /**
  * @group Attributes
  */
  public function testAttrWrite() {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//group/item')
      ->attr('index', '15')
      ->attr('index');
    $this->assertEquals('15', $fd);
  }

  /**
  * @group Attributes
  * @dataProvider getInvalidAttributeNames
  */
  public function testAttrWriteWithInvalidNames($attrName) {
    try {
      $this->getFixtureFromString(self::XML)
        ->find('//item')
        ->attr($attrName, '');
      $this->fail('An expected exception has not been raised.');
    } catch (UnexpectedValueException $expected) {
    }
  }

  public static function getInvalidAttributeNames() {
    return array(
      array('1foo'),
      array('1bar:foo'),
      array('bar:1foo'),
      array('bar:foo<>'),
      array('bar:'),
      array(':foo')
    );
  }

  /**
  * @group Attributes
  * @dataProvider getValidAttributeNames
  */
  public function testAttrWriteWithValidNames($attrName) {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//item')
      ->attr($attrName, 'foo');
    $this->assertTrue($fd->item(0)->hasAttribute($attrName));
    $this->assertEquals('foo', $fd->item(0)->getAttribute($attrName));
  }

  public static function getValidAttributeNames() {
    return array(
      array('foo'),
      array('bar:foo')
    );
  }

  /**
  * @group Attributes
  */
  public function testAttrWriteWithArray() {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//group/item')
      ->attr(array('index' => '15', 'length' => '34', 'label' => 'box'));
    $this->assertEquals('15', $fd->attr('index'));
    $this->assertEquals('34', $fd->attr('length'));
    $this->assertEquals('box', $fd->attr('label'));
  }

  /**
  * @group Attributes
  */
  public function testAttrWriteWithCallback() {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//group/item')
      ->attr('index', array($this, 'callbackForAttr'));
    $this->assertEquals('Callback #0', $fd->attr('index'));
  }

  /**
  * @group Attributes
  */
  public function testRemoveAttr() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->removeAttr('index');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Attributes
  */
  public function testRemoveAttrWithInvalidParameter() {
    $fd = new FluentDOM();
    try {
      $fd->removeAttr(1);
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group Attributes
  */
  public function testRemoveAttrWithListParameter() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->removeAttr(array('index', 'style'));
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Attributes
  */
  public function testRemoveAttrWithAsteriskParameter() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->removeAttr('*');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Attributes - Classes
  */

  /**
  * @group Attributes
  */
  public function testHasClassExpectingTrue() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $this->assertTrue($fd->hasClass('test1'));
  }

  /**
  * @group Attributes
  */
  public function testHasClassExpectingFalse() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $this->assertFalse($fd->hasClass('INVALID_CLASSNAME'));
  }

  /**
  * @group Attributes
  */
  public function testAddClass() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->addClass('added');
    $this->assertTrue($fd->hasClass('added'));
  }

  /**
  * @group Attributes
  */
  public function testRemoveClass() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->removeClass('test2');
    $this->assertEquals('test1', $fd[0]->getAttribute('class'));
    $this->assertFalse($fd[1]->hasAttribute('class'));
  }

  /**
  * @group Attributes
  */
  public function testRemoveClassWithEmptyString() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->removeClass();
    $this->assertFalse($fd[0]->hasAttribute('class'));
    $this->assertFalse($fd[1]->hasAttribute('class'));
  }

  /**
  * @group Attributes
  * @dataProvider dataProviderToggleClass
  */
  public function testToggleClass($toggle, $mode, $expectedOne, $expectedTwo) {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->toggleClass($toggle);
    $this->assertEquals($expectedOne, $fd[0]->getAttribute('class'));
    $this->assertEquals($expectedTwo, $fd[1]->getAttribute('class'));
  }

  /**
  * @group Attributes
  */
  public function testToogleClassWithCallback() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->toggleClass(array($this, 'callbackForToggleClass'));
    $this->assertEquals('test4', $fd[0]->getAttribute('class'));
    $this->assertEquals('test4', $fd[1]->getAttribute('class'));
  }

  /**
  * Data Provider
  */
  public function dataProviderToggleClass() {
    return array(
      array('test1', NULL, 'test2', 'test2 test1'),
      array('test2 test4', NULL, 'test1 test4', 'test4')
    );
  }

  /*
  * Callbacks
  */

  /**
  * @uses testAppendWithCallback
  */
  public function callbackForAppend($index, $html) {
    return strrev($html);
  }

  /**
  * @uses testAppendOnEmptyDocumentWithCallback
  */
  public function callbackForAppendNode($index, $html) {
    return '<sample>Hello World</sample>';
  }

  /**
  * @uses testAttrWriteWithCallback
  */
  public function callbackForAttr($index, $value) {
    return 'Callback #'.$value;
  }

  /**
  * @uses testTextWriteWithCallback
  */
  public function callbackForText($index, $text) {
    return 'Callback #'.$index.': '.$text;
  }

  /**
  * @uses testXmlWriteWithCallback
  */
  public function callbackForXml($index, $xml) {
    if ($index == 1) {
      return '';
    } else {
      return strtoupper($xml);
    }
  }

  /**
  * @uses testNotWithFunction()
  */
  public function callbackTestNotWithFunction($node, $index) {
    return $node->nodeName != "items";
  }

  /**
  * @uses testFilterWithFunction()
  */
  public function callbackTestFilterWithFunction($node, $index) {
    return $node->nodeName == "items";
  }

  /**
  * @uses testToogleClassWithCallback()
  */
  public function callbackForToggleClass($index, $class) {
    return $class.' test4';
  }
}
?>