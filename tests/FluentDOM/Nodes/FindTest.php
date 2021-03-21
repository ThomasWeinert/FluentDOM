<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class NodesFindTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFind(): void {
      $fd = (new Nodes(self::XML))->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = $fd->find('group/item');
      $this->assertEquals(3, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFinForcingSort(): void {
      $fd = (new Nodes(self::XML))->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = $fd->find('group/item', \FluentDOM\Nodes::FIND_FORCE_SORT);
      $this->assertEquals(3, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindWithCallableSelector(): void {
      $fd = (new Nodes(self::XML))->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = $fd->find(
        function ($context) use ($fd) {
          return $fd->xpath()->evaluate('name() = "item"', $context);
        }
      );
      $this->assertEquals(3, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindWithCallableSelectorReturningFalse(): void {
      $fd = (new Nodes(self::XML))->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = $fd->find(
        function ($context) use ($fd) {
          return FALSE;
        }
      );
      $this->assertEquals(0, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindWithSelectorCallback(): void {
      $fd = new Nodes(self::XML);
      $fd->onPrepareSelector = function() {return '//item'; };
      $fd = $fd->find('/*');
      $this->assertEquals(3, $fd->length);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindUsingFilterModeWithSelectorCallback(): void {
      $fd = new Nodes(self::XML);
      $fd->onPrepareSelector = function() {return 'self::item'; };
      $fd = $fd->find('', \FluentDOM\Nodes::FIND_MODE_FILTER);
      $this->assertEquals(3, $fd->length);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindUsingFilterModeWithSelectorCallbackIgnoreRootDescendantFix(): void {
      $fd = new Nodes(self::XML);
      $fd->onPrepareSelector = function() { return '//self::item'; };
      $fd = $fd->find('', \FluentDOM\Nodes::FIND_MODE_FILTER);
      $this->assertEquals(3, $fd->length);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindUsingFilterModeWithSelectorCallbackAddSelfAxeFix(): void {
      $fd = new Nodes(self::XML);
      $fd->onPrepareSelector = function() { return '//item'; };
      $fd = $fd->find('', \FluentDOM\Nodes::FIND_MODE_FILTER);
      $this->assertEquals(3, $fd->length);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindFromRootNode(): void {
      $fd = (new Nodes(self::XML))->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = (new Nodes(self::XML))->find('/items');
      $this->assertEquals(1, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindWithNode(): void {
      $fd = new Nodes(self::XML);
      $fd = $fd->find($fd->document->documentElement);
      $this->assertEquals('items', $fd->item(0)->nodeName);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindWithNodeList(): void {
      $fd = new Nodes(self::XML);
      $fd = $fd->find([$fd->document->documentElement]);
      $this->assertEquals('items', $fd->item(0)->nodeName);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindWithExpressionThatReturnsScalarExpectingException(): void {
      $fd = new Nodes(self::XML);
      $this->expectException(
        \InvalidArgumentException::class,
        'Given selector/expression did not return a node list.'
      );
      $fd->find('count(*)');
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Nodes::find
     * @covers \FluentDOM\Nodes::fetch
     * @covers \FluentDOM\Nodes::prepareFindContext
     * @covers \FluentDOM\Nodes::prepareSelectorAsFilter
     */
    public function testFindWithInvalidSelectorExpectingException(): void {
      $fd = new Nodes(self::XML);
      $this->expectException(
        \InvalidArgumentException::class,
        'Invalid selector/expression.'
      );
      $fd->find('');
    }
  }
}
