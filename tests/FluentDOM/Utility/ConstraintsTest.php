<?php
namespace FluentDOM\Utility {

  require_once(__DIR__ . '/../TestCase.php');

  use FluentDOM\TestCase;

  class ConstraintsTest extends TestCase {

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideValidNodes
     * @covers \FluentDOM\Utility\Constraints::filterNode
     * @param mixed $node
     */
    public function testFilterNodeExpectingNode($node) {
      $this->assertInstanceOf(\DOMNode::class, Constraints::filterNode($node));
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
     * @covers \FluentDOM\Utility\Constraints::filterNode
     * @dataProvider provideInvalidNodes
     * @param mixed $node
     * @param bool $ignoreTextNodes
     */
    public function testFilterNodeExpectingNull($node, $ignoreTextNodes = FALSE) {
      $this->assertNull(Constraints::filterNode($node, $ignoreTextNodes));
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
     * @covers \FluentDOM\Utility\Constraints::assertNode
     * @param $node
     */
    public function testAssertNodeExpectingNode($node) {
      $this->assertTrue(Constraints::assertNode($node));
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::assertNode
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
     * @covers \FluentDOM\Utility\Constraints::assertNode
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
     * @covers \FluentDOM\Utility\Constraints::filterNodeList
     * @param $list
     */
    public function testFilterNodeListExpectingList($list) {
      $this->assertThat(
        Constraints::filterNodeList($list),
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
     * @covers \FluentDOM\Utility\Constraints::filterNodeList
     */
    public function testFilterNodeListExpectingNull() {
      $this->assertNull(Constraints::filterNodeList('string'));
    }

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideCallables
     * @covers \FluentDOM\Utility\Constraints::filterCallable
     * @covers \FluentDOM\Utility\Constraints::filterCallableArray
     * @param $callable
     */
    public function testFilterCallable($callable) {
      $this->assertInternalType(
        'callable', Constraints::filterCallable($callable)
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
     * @covers \FluentDOM\Utility\Constraints::filterCallable
     */
    public function testFilterCallableWithGlobalFunctionExpectingCallable() {
      $this->assertInternalType(
        'callable', Constraints::filterCallable('strpos', TRUE)
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::filterCallable
     */
    public function testFilterCallableWithGlobalFunctionExpectingNull() {
      $this->assertNull(Constraints::filterCallable('strpos', FALSE));
    }

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideInvalidCallables
     * @covers \FluentDOM\Utility\Constraints::filterCallable
     * @covers \FluentDOM\Utility\Constraints::filterCallableArray
     * @param $callback
     */
    public function testFilterCallableExpectingNull($callback) {
      $this->assertNull(Constraints::filterCallable($callback));
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
     * @covers \FluentDOM\Utility\Constraints::filterCallable
     */
    public function testFilterCallableExpectingException() {
      $this->expectException(\InvalidArgumentException::class);
      Constraints::filterCallable(NULL, FALSE, FALSE);
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::hasOption
     */
    public function testHasOptionExpectingTrue() {
      $this->assertTrue(
        Constraints::hasOption(3, 2)
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::hasOption
     */
    public function testHasOptionExpectingFalse() {
      $this->assertFalse(
        Constraints::hasOption(3, 4)
      );
    }
  }
}