<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Utility {

  require_once __DIR__ . '/../TestCase.php';

  use FluentDOM\DOM\Element;
  use FluentDOM\TestCase;

  class ConstraintsTest extends TestCase {

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideValidNodes
     * @covers \FluentDOM\Utility\Constraints::filterNode
     * @param mixed $node
     */
    public function testFilterNodeExpectingNode($node): void {
      $this->assertInstanceOf(\DOMNode::class, Constraints::filterNode($node));
    }

    public static function provideValidNodes() {
      $document = new \DOMDocument();
      return [
        [$document->createElement('element')],
        [$document->createTextNode('text')],
        [$document->createCDATASection('text')]
      ];
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
      return [
        ['string'],
        [$document->createTextNode('text'), TRUE],
        [$document->createCDATASection('text'), TRUE]
      ];
    }

    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideValidNodes
     * @covers \FluentDOM\Utility\Constraints::assertNode
     * @param $node
     */
    public function testAssertNodeExpectingNode($node): void {
      $this->assertTrue(Constraints::assertNode($node));
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::assertNode
     */
    public function testAssertNodeExpectingException(): void {
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
    public function testAssertNodeExpectingExceptionWithModifiedMessage(): void {
      $this->expectException(
        \InvalidArgumentException::class,
        'Not a node but a stdClass.'
      );
      Constraints::assertNode(new \stdClass, 'Not a node but a %s.');
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::assertNodeClass
     */
    public function testAssertNodeClassWithMatchingClass(): void {
      $document = new \DOMDocument();
      $this->assertTrue(
        Constraints::assertNodeClass($document, [\DOMElement::class, \DOMDocument::class])
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::assertNodeClass
     */
    public function testAssertNodeClassExpectingException(): void {
      $document = new \DOMDocument();
      $this->expectException(\LogicException::class);
      $this->expectExceptionMessage('Unexpected node type: DOMDocument');
      Constraints::assertNodeClass($document, \DOMElement::class);
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::assertNodeClass
     */
    public function testAssertNodeClassExpectingExceptionWithProvidedMessage(): void {
      $document = new \DOMDocument();
      $this->expectException(\LogicException::class);
      $this->expectExceptionMessage('Expect DOMElement not DOMDocument');
      Constraints::assertNodeClass($document, \DOMElement::class, 'Expect DOMElement not %s');
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::assertNodeClass
     */
    public function testAssertNodeClassWithMultipleClassesExpectingException(): void {
      $document = new \DOMDocument();
      $this->expectException(\LogicException::class);
      Constraints::assertNodeClass($document, [\DOMElement::class, \DOMAttr::class]);
    }


    /**
     * @group Utility
     * @group Constraints
     * @dataProvider provideNodeLists
     * @covers \FluentDOM\Utility\Constraints::filterNodeList
     * @param $list
     */
    public function testFilterNodeListExpectingList($list): void {
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
      return [
        [[$document->createElement('element')]],
        [$document->getElementsByTagName('text')]
      ];
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::filterNodeList
     */
    public function testFilterNodeListExpectingNull(): void {
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
    public function testFilterCallable($callable): void {
      $this->assertIsCallable(Constraints::filterCallable($callable));
    }

    public function provideCallables() {
      return [
        [static function() {}],
        [[$this, 'provideCallables']]
      ];
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::filterCallable
     */
    public function testFilterCallableWithGlobalFunctionExpectingCallable(): void {
      $this->assertIsCallable(
        Constraints::filterCallable('strpos', TRUE)
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::filterCallable
     */
    public function testFilterCallableWithGlobalFunctionExpectingNull(): void {
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
    public function testFilterCallableExpectingNull($callback): void {
      $this->assertNull(Constraints::filterCallable($callback));
    }

    public function provideInvalidCallables() {
      return [
        [NULL],
        [[]],
        [[1, 2, 3]]
      ];
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::filterCallable
     */
    public function testFilterCallableExpectingException(): void {
      $this->expectException(\InvalidArgumentException::class);
      Constraints::filterCallable(NULL, FALSE, FALSE);
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::hasOption
     */
    public function testHasOptionExpectingTrue(): void {
      $this->assertTrue(
        Constraints::hasOption(3, 2)
      );
    }

    /**
     * @group Utility
     * @group Constraints
     * @covers \FluentDOM\Utility\Constraints::hasOption
     */
    public function testHasOptionExpectingFalse(): void {
      $this->assertFalse(
        Constraints::hasOption(3, 4)
      );
    }
  }
}
