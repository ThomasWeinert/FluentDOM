<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class ConstraintsTest extends TestCase {

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideValidNodes
     * @covers \FluentDOM\Constraints::isNode
     */
    public function testIsNodeExpectingNode($node) {
      $this->assertInstanceOf(\DOMNode::class, Constraints::isNode($node));
    }

    public static function provideValidNodes() {
      $document = new \DOMDocument();
      return array(
        array($document->createElement('element')),
        array($document->createTextNode('text')),
        array($document->createCDATASection('text'))
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::isNode
     * @dataProvider provideInvalidNodes
     */
    public function testIsNodeExpectingNull($node, $ignoreTextNodes = FALSE) {
      $this->assertNull(Constraints::isNode($node, $ignoreTextNodes));
    }

    public static function provideInvalidNodes() {
      $document = new \DOMDocument();
      return array(
        array('string'),
        array($document->createTextNode('text'), TRUE),
        array($document->createCDATASection('text'), TRUE)
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideValidNodes
     * @covers \FluentDOM\Constraints::assertNode
     */
    public function testAssertNodeExpectingNode($node) {
      $this->assertTrue(Constraints::assertNode($node));
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::assertNode
     */
    public function testAssertNodeExpectingException() {
      $this->expectException(
        \InvalidArgumentException::class,
        'DOMNode expected, got: boolean.'
      );
      Constraints::assertNode(FALSE);
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::assertNode
     */
    public function testAssertNodeExpectingExceptionWithModifiedMessage() {
      $this->expectException(
        \InvalidArgumentException::class,
        'Not a node but a stdClass.'
      );
      Constraints::assertNode(new \stdClass, 'Not a node but a %s.');
    }


    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideNodeLists
     * @covers \FluentDOM\Constraints::isNodeList
     */
    public function testIsNodeListExpectingList($list) {
      $this->assertThat(
        Constraints::isNodeList($list),
        $this->logicalOr(
          $this->isType('array'),
          $this->isInstanceOf(\Traversable::class)
        )
      );
    }

    public static function provideNodeLists() {
      $document = new \DOMDocument();
      return array(
        array(array($document->createElement('element'))),
        array($document->getElementsByTagName('text'))
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::isNodeList
     */
    public function testIsNodeListExpectingNull() {
      $this->assertNull(Constraints::isNodeList('string'));
    }

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideCallables
     * @covers \FluentDOM\Constraints::isCallable
     * @covers \FluentDOM\Constraints::isCallableArray
     */
    public function testIsCallable($callable) {
      $this->assertInternalType(
        'callable', Constraints::isCallable($callable)
      );
    }

    public function provideCallables() {
      return array(
        array(function() {}),
        array(array($this, 'provideCallables'))
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::isCallable
     */
    public function testIsCallableWithGlobalFunctionExpectingCallable() {
      $this->assertInternalType(
        'callable', Constraints::isCallable('strpos', TRUE)
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::isCallable
     */
    public function testIsCallableWithGlobalFunctionExpectingNull() {
      $this->assertNull(Constraints::isCallable('strpos', FALSE));
    }

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideInvalidCallables
     * @covers \FluentDOM\Constraints::isCallable
     * @covers \FluentDOM\Constraints::isCallableArray
     */
    public function testIsCallableExpectingNull($callable) {
      $this->assertNull(Constraints::isCallable(NULL));
    }

    public function provideInvalidCallables() {
      return array(
        array(NULL),
        array(array()),
        array(array(1, 2, 3))
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::isCallable
     */
    public function testIsCallableExpectingException() {
      $this->expectException(\InvalidArgumentException::class);
      Constraints::isCallable(NULL, FALSE, FALSE);
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::hasOption
     */
    public function testHasOptionExpectingTrue() {
      $this->assertTrue(
        Constraints::hasOption(3, 2)
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Constraints::hasOption
     */
    public function testHasOptionExpectingFalse() {
      $this->assertFalse(
        Constraints::hasOption(3, 4)
      );
    }
  }
}