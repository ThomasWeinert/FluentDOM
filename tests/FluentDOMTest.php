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
        <div>class testing</div>
      </html>
    </items>
  ';

  /**
  * @group GlobalFunctions
  */
  public function testFunction() {
    $fd = FluentDOM();
    $this->assertTrue($fd instanceof FluentDOM);
  }

  /**
  * @group GlobalFunctions
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
  * @group Core
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
  * @group Core
  * @covers FluentDOM::toArray
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

  /**
  * @group Core
  * @covers FluentDOM::node
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
  * @group Core
  * @covers FluentDOM::node
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
  * @group Core
  * @covers FluentDOM::node
  */
  public function testNodeWithDomelement() {
    $fd = $this->getFixtureFromString(self::XML);
    $nodes = $fd->node($fd->document->createElement('div'));
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertEquals(1, count($nodes));
  }

  /**
  * @group Core
  * @covers FluentDOM::node
  */
  public function testNodeWithDomtext() {
    $fd = $this->getFixtureFromString(self::XML);
    $nodes = $fd->node($fd->document->createTextNode('div'));
    $this->assertTrue($fd instanceof FluentDOM);
    $this->assertEquals(1, count($nodes));
  }

  /**
  * @group Core
  * @covers FluentDOM::node
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
  * @group Core
  * @covers FluentDOM::node
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
  * @group Core
  * @covers FluentDOM::node
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

  /**
  * @group Traversing
  * @covers FluentDOM::each
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
  * @group Traversing
  * @covers FluentDOM::each
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

  /*
  * Traversing - Filtering
  */

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::eq
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::eq
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::filter
  */
  public function testFilter() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $filterFd = $fd->filter('name() = "items"');
    $this->assertEquals(1, $filterFd->length);
    $this->assertTrue($filterFd !== $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::filter
  */
  public function testFilterWithFunction() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $filterFd = $fd->filter(array($this, 'callbackTestFilterWithFunction'));
    $this->assertEquals(1, $filterFd->length);
    $this->assertTrue($filterFd !== $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::has
  */
  public function testHas() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//li')
      ->has('name() = "ul"')
      ->addClass('withSubList');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::has
  */
  public function testHasWithNode() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $node = $fd->find('//ul')->item(1);
    $fd
      ->find('//li')
      ->has($node)
      ->addClass('withSubList');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

   /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::first
  */
  public function testFirst() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $fdFilter = $fd->first();
    $this->assertSame('0', $fdFilter->item(0)->getAttribute('index'));
    $this->assertNotSame($fd, $fdFilter);
  }

   /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::last
  */
  public function testLast() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $fdFilter = $fd->last();
    $this->assertSame('2', $fdFilter->item(0)->getAttribute('index'));
    $this->assertNotSame($fd, $fdFilter);
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::get
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::get
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::get
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::get
  */
  public function testGetWithInvalidPosition() {
    $fd = $this->getFixtureFromString(self::XML)->find('/*');
    $this->assertSame(
      array(),
      $fd->get(99)
    );
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::is
  */
  public function testIs() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $this->assertTrue($fd->is('name() = "items"'));
    $this->assertFalse($fd->is('name() = "invalidItemName"'));
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::is
  */
  public function testIsOnEmptyList() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertTrue($fd->length == 0);
    $this->assertFalse($fd->is('name() = "items"'));
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::map
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::map
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::map
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::not
  */
  public function testNot() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $notDoc = $fd->not('name() != "items"');
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::not
  */
  public function testNotWithFunction() {
    $fd = $this->getFixtureFromString(self::XML)->find('//*');
    $this->assertTrue($fd->length > 1);
    $notDoc = $fd->not(array($this, 'callbackTestNotWithFunction'));
    $this->assertEquals(1, $notDoc->length);
    $this->assertTrue($notDoc !== $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::slice
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::slice
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
  * @group Traversing
  * @group TraversingFilter
  * @covers FluentDOM::slice
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::add
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::add
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::add
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::add
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::add
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::children
  */
  public function testChildren() {
    $fd = $this->getFixtureFromFile(__FUNCTION__)
      ->find('//div[@id = "container"]/p')
      ->children();
    $this->assertEquals(2, $fd->length);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::children
  */
  public function testChildrenExpression() {
    $fd = $this->getFixtureFromFile(__FUNCTION__)
      ->find('//div[@id = "container"]/p')
      ->children('name() = "em"');
    $this->assertEquals(1, $fd->length);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::contents
  */
  public function testContents() {
    $fd = $this->getFixtureFromFile(__FUNCTION__)
      ->find('//div[@id = "container"]/p')
      ->contents();
    $this->assertEquals(5, $fd->length);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::find
  */
  public function testFind() {
    $fd = $this->getFixtureFromString(self::XML)->find('/*');
    $this->assertEquals(1, $fd->length);
    $findFd = $fd->find('group/item');
    $this->assertEquals(3, $findFd->length);
    $this->assertTrue($findFd !== $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::find
  */
  public function testFindFromRootNode() {
    $fd = $this->getFixtureFromString(self::XML)->find('/*');
    $this->assertEquals(1, $fd->length);
    $findFd = $this->getFixtureFromString(self::XML)->find('/items');
    $this->assertEquals(1, $findFd->length);
    $this->assertTrue($findFd !== $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::find
  */
  public function testFindWithNamespaces() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $doc = $fd ->find('//_:entry');
    $this->assertEquals(25, $doc->length);
    $value = $fd ->find('//openSearch:totalResults')->text();
    $this->assertEquals(38, $value);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::next
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::nextAll
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::nextUntil
  */
  public function testNextUntil() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//*[@id = "term-2"]')
      ->nextUntil('name() = "dt"')
      ->addClass('next');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::parent
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::parents
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::parentsUntil
  */
  public function testParentsUntil() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//li[contains(concat(" ", normalize-space(@class), " "),  " item-a ")]')
      ->parentsUntil('contains(concat(" ", normalize-space(@class), " "),  " level-1 ")')
      ->addClass('selectedParent');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::prev
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::prev
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::prevAll
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::prevAll
  */
  public function testPrevAllExpression() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//div[@class= "here"]')
      ->prevAll('.//span')
      ->addClass('nextTest');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::prevUntil
  */
  public function testPrevUntil() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//*[@id = "term-2"]')
      ->prevUntil('name() = "dt"')
      ->addClass('previous');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::siblings
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
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::closest
  */
  public function testClosest() {
    $attribute = $this->getFixtureFromString(self::XML)
      ->find('//item')
      ->closest('name() = "group"')
      ->attr("id");
    $this->assertEquals('1st', $attribute);
  }

  /**
  * @group Traversing
  * @group TraversingFind
  * @covers FluentDOM::closest
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
  * @group Traversing
  * @group TraversingChaining
  * @covers FluentDOM::andSelf
  */
  public function testAndSelf() {
    $fd = $this->getFixtureFromString(self::XML)->find('/items')->find('.//item');
    $this->assertEquals(3, $fd->length);
    $andSelfFd = $fd->andSelf();
    $this->assertEquals(4, $andSelfFd->length);
    $this->assertTrue($andSelfFd !== $fd);
  }

  /**
  * @group Traversing
  * @group TraversingChaining
  * @covers FluentDOM::end
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

  /*
  * Manipulation - Inserting Inside
  */

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::xml
  */
  public function testXmlRead() {
    $expect = '<item index="0">text1</item>'.
      '<item index="1">text2</item>'.
      '<item index="2">text3</item>';
    $xml = $this->getFixtureFromString(self::XML)->find('//group')->xml();
    $this->assertEquals($expect, $xml);
  }

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::xml
  */
  public function testXmlReadEmpty() {
    $xml = $this->getFixtureFromString('<items/>')->find('/items/*')->xml();
    $this->assertEquals('', $xml);
  }

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::xml
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
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::xml
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
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::xml
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
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::text
  */
  public function testTextRead() {
    $expect = 'text1text2text3';
    $text = $this->getFixtureFromString(self::XML)->formatOutput()->find('//group')->text();
    $this->assertEquals($expect, $text);
  }

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::text
  */
  public function testTextWrite() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $textFd = $fd->text('changed');
    $this->assertEquals('changed', $fd[0]->textContent);
    $this->assertEquals('changed', $fd[1]->textContent);
    $this->assertTrue($fd === $textFd);
  }

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::text
  */
  public function testTextWriteWithCallback() {
    $fd = $this->getFixtureFromString(self::XML)->find('//item');
    $textFd = $fd->text(array($this, 'callbackForText'));
    $this->assertEquals('Callback #0: text1', $fd[0]->textContent);
    $this->assertEquals('Callback #1: text2', $fd[1]->textContent);
    $this->assertTrue($fd === $textFd);
  }

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::append
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
  * @group ManipulationInside
  * @covers FluentDOM::append
  */
  public function testAppendDomelement() {
    $fd = new FluentDOM();
    $fd->append('<strong>Hello</strong>');
    $this->assertEquals('strong', $fd->find('/strong')->item(0)->nodeName);
  }

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::append
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
  * @group ManipulationInside
  * @covers FluentDOM::append
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
  * @group ManipulationInside
  * @covers FluentDOM::append
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
  * @group ManipulationInside
  * @covers FluentDOM::appendTo
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
  * @group ManipulationInside
  * @covers FluentDOM::prepend
  */
  public function testPrepend() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->prepend('<strong>Hello</strong>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::prepend
  */
  public function testPrependWithCallback() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->prepend(array($this, 'callbackForPrepend'));
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  * @group ManipulationInside
  * @covers FluentDOM::prependTo
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
  * @group ManipulationOutside
  * @covers FluentDOM::after
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
  * @group ManipulationOutside
  * @covers FluentDOM::after
  */
  public function testAfterWithFunction() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd ->formatOutput()
      ->find('//p')
      ->after(array($this, 'callbackForAfter'));
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  * @group ManipulationOutside
  * @covers FluentDOM::before
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
  * @group ManipulationOutside
  * @covers FluentDOM::before
  */
  public function testBeforeWithFunction() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->formatOutput()
      ->find('//p')
      ->before(array($this, 'callbackForBefore'));
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  * @group ManipulationOutside
  * @covers FluentDOM::insertAfter
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
  * @group ManipulationOutside
  * @covers FluentDOM::insertBefore
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
  * @group ManipulationAround
  * @covers FluentDOM::_wrap
  * @covers FluentDOM::wrap
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
  * @group ManipulationAround
  * @covers FluentDOM::_wrap
  * @covers FluentDOM::wrap
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
  * @group ManipulationAround
  * @covers FluentDOM::_wrap
  * @covers FluentDOM::wrap
  */
  public function testWrapWithDomnodelist() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $divs = $fd->xpath->query('//div[@class = "wrapper"]');
    $fd->find('//p')->wrap($divs);
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  * @group ManipulationAround
  * @covers FluentDOM::_wrap
  * @covers FluentDOM::wrap
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
  * @group ManipulationAround
  * @covers FluentDOM::_wrap
  * @covers FluentDOM::wrap
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
  * @group ManipulationAround
  * @covers FluentDOM::_wrap
  * @covers FluentDOM::wrap
  */
  public function testWrapWithCallback() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd->find('//p')->wrap(array($this, 'callbackTestWrapWithFunction'));
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  * @group ManipulationAround
  * @covers FluentDOM::wrapAll
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
  * @group ManipulationAround
  * @covers FluentDOM::wrapAll
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
  * @group ManipulationAround
  * @covers FluentDOM::_wrap
  * @covers FluentDOM::wrapInner
  */
  public function testWrapInner() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->wrapInner('<b></b>');
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  * @group ManipulationAround
  * @covers FluentDOM::_wrap
  * @covers FluentDOM::wrapInner
  */
  public function testWrapInnerWithCallback() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->wrapInner(array($this, 'callbackTestWrapInnerWithFunction'));
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /*
  * Manipulation - Replacing
  */

  /**
  * @group Manipulation
  * @group ManipulationReplace
  * @covers FluentDOM::replaceWith
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
  * @group ManipulationReplace
  * @covers FluentDOM::replaceWith
  */
  public function testReplaceWithWithFunction() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd
      ->find('//p')
      ->replaceWith(array($this, 'callbackForReplaceWith'));
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }

  /**
  * @group Manipulation
  * @group ManipulationReplace
  * @covers FluentDOM::replaceAll
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
  * @group ManipulationReplace
  * @covers FluentDOM::replaceAll
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
  * @group ManipulationReplace
  * @covers FluentDOM::replaceAll
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
  * @group ManipulationRemove
  * @covers FluentDOM::__call
  * @covers FluentDOM::_emptyNodes
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
  * @group ManipulationRemove
  * @covers FluentDOM::remove
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
  * @group ManipulationRemove
  * @covers FluentDOM::remove
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
  * @group ManipulationCopy
  * @covers FluentDOM::__call
  * @covers FluentDOM::_cloneNodes
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
  * @covers FluentDOM::attr
  */
  public function testAttrRead() {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//group/item')
      ->attr('index');
    $this->assertEquals('0', $fd);
  }

  /**
  * @group Attributes
  * @covers FluentDOM::attr
  */
  public function testAttrReadFromRoot() {
    $fd = $this->getFixtureFromString(self::XML);
    $this->assertEquals('1.0', $fd->find('/*')->attr('version'));
    $this->assertEquals('1.0', $fd->find('/items')->attr('version'));
    $this->assertEquals('1.0', $fd->find('//items')->attr('version'));
  }

  /**
  * @group Attributes
  * @covers FluentDOM::attr
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
  * @covers FluentDOM::attr
  */
  public function testAttrReadNoMatch() {
    $fd = $this->getFixtureFromString(self::XML)->attr('index');
    $this->assertTrue(empty($fd));
  }

  /**
  * @group Attributes
  * @covers FluentDOM::attr
  */
  public function testAttrReadOnDomtext() {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//item/text()')
      ->attr('index');
    $this->assertTrue(empty($fd));
  }

  /**
  * @group Attributes
  * @covers FluentDOM::attr
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
  * @dataProvider dataProviderInvalidAttributeNames
  * @covers FluentDOM::attr
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

  public static function dataProviderInvalidAttributeNames() {
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
  * @dataProvider dataProviderValidAttributeNames
  * @covers FluentDOM::attr
  */
  public function testAttrWriteWithValidNames($attrName) {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//item')
      ->attr($attrName, 'foo');
    $this->assertTrue($fd->item(0)->hasAttribute($attrName));
    $this->assertEquals('foo', $fd->item(0)->getAttribute($attrName));
  }

  public static function dataProviderValidAttributeNames() {
    return array(
      array('foo'),
      array('bar:foo')
    );
  }

  /**
  * @group Attributes
  * @covers FluentDOM::attr
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
  * @covers FluentDOM::attr
  */
  public function testAttrWriteWithCallback() {
    $fd = $this->getFixtureFromString(self::XML)
      ->find('//group/item')
      ->attr('index', array($this, 'callbackForAttr'));
    $this->assertEquals('Callback #0', $fd->attr('index'));
  }

  /**
  * @group Attributes
  * @covers FluentDOM::removeAttr
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
  * @covers FluentDOM::removeAttr
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
  * @covers FluentDOM::removeAttr
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
  * @covers FluentDOM::removeAttr
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
  * @group AttributesClasses
  * @covers FluentDOM::hasClass
  */
  public function testHasClassExpectingTrue() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $this->assertTrue($fd->hasClass('test1'));
  }

  /**
  * @group Attributes
  * @group AttributesClasses
  * @covers FluentDOM::hasClass
  */
  public function testHasClassExpectingFalse() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $this->assertFalse($fd->hasClass('INVALID_CLASSNAME'));
  }

  /**
  * @group Attributes
  * @group AttributesClasses
  * @covers FluentDOM::toggleClass
  * @covers FluentDOM::addClass
  */
  public function testAddClass() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->addClass('added');
    $this->assertTrue($fd->hasClass('added'));
  }

  /**
  * @group Attributes
  * @group AttributesClasses
  * @covers FluentDOM::toggleClass
  * @covers FluentDOM::removeClass
  */
  public function testRemoveClass() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->removeClass('test2');
    $this->assertEquals('test1', $fd[0]->getAttribute('class'));
    $this->assertFalse($fd[1]->hasAttribute('class'));
  }

  /**
  * @group Attributes
  * @group AttributesClasses
  * @covers FluentDOM::toggleClass
  * @covers FluentDOM::removeClass
  */
  public function testRemoveClassWithEmptyString() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->removeClass();
    $this->assertFalse($fd[0]->hasAttribute('class'));
    $this->assertFalse($fd[1]->hasAttribute('class'));
  }

  /**
  * @group Attributes
  * @group AttributesClasses
  * @dataProvider dataProviderToggleClass
  * @covers FluentDOM::toggleClass
  */
  public function testToggleClass($toggle, $expectedOne, $expectedTwo) {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->toggleClass($toggle);
    $this->assertEquals($expectedOne, $fd[0]->getAttribute('class'));
    $this->assertEquals($expectedTwo, $fd[1]->getAttribute('class'));
    $this->assertEquals($toggle, $fd[2]->getAttribute('class'));
  }

  public function dataProviderToggleClass() {
    return array(
      array('test1', 'test2', 'test2 test1'),
      array('test2 test4', 'test1 test4', 'test4')
    );
  }

  /**
  * @group Attributes
  * @group AttributesClasses
  * @covers FluentDOM::toggleClass
  */
  public function testToogleClassWithCallback() {
    $fd = $this->getFixtureFromString(self::XML)->find('//html/div');
    $fd->toggleClass(array($this, 'callbackForToggleClass'));
    $this->assertEquals('test4', $fd[0]->getAttribute('class'));
    $this->assertEquals('test4', $fd[1]->getAttribute('class'));
  }

  /*
  * Callbacks
  */

  /**
  * @uses testAppendWithCallback
  */
  public function callbackForAppend($node, $index, $content) {
    return strrev($content);
  }

  /**
  * @uses testAppendOnEmptyDocumentWithCallback
  */
  public function callbackForAppendNode($node, $index, $content) {
    return '<sample>Hello World</sample>';
  }

  /**
  * @uses testAfterWithFunction
  */
  public function callbackForAfter($node, $index, $content) {
    return '<p index="'.$index.'">Hi</p>';
  }

  /**
  * @uses testBeforeWithFunction
  */
  public function callbackForBefore($node, $index, $content) {
    return '<p index="'.$index.'">Hi</p>';
  }

  /**
  * @uses testPrependWithCallback
  */
  public function callbackForPrepend($node, $index, $content) {
    return 'Hello #'.($index + 1);
  }

  /**
  * @uses testReplaceWithWithFunction
  */
  public function callbackForReplaceWith($node, $index, $content) {
    return '<div index="'.$index.'">'.$node->textContent.'</div>';
  }

  /**
  * @uses testAttrWriteWithCallback
  */
  public function callbackForAttr($node, $index, $content) {
    return 'Callback #'.$content;
  }

  /**
  * @uses testTextWriteWithCallback
  */
  public function callbackForText($node, $index, $text) {
    return 'Callback #'.$index.': '.$text;
  }

  /**
  * @uses testXmlWriteWithCallback
  */
  public function callbackForXml($node, $index, $xml) {
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
  * @uses testWrapWithFunction()
  */
  public function callbackTestWrapWithFunction($node, $index) {
    return '<div class="'.$node->textContent.'_'.$index.'" />';
  }

  /**
  * @uses testWrapInnerWithFunction()
  */
  public function callbackTestWrapInnerWithFunction($node, $index) {
    return '<b class="'.$node->textContent.'_'.$index.'" />';
  }

  /**
  * @uses testToogleClassWithCallback()
  */
  public function callbackForToggleClass($node, $index, $class) {
    return $class.' test4';
  }
}
?>