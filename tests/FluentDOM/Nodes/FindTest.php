<?php
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
    public function testFind() {
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
    public function testFinForcingSort() {
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
    public function testFindWithCallableSelector() {
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
    public function testFindWithCallableSelectorReturningFalse() {
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
    public function testFindWithSelectorCallback() {
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
    public function testFindUsingFilterModeWithSelectorCallback() {
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
    public function testFindUsingFilterModeWithSelectorCallbackIgnoreRootDescendantFix() {
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
    public function testFindUsingFilterModeWithSelectorCallbackAddSelfAxeFix() {
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
    public function testFindFromRootNode() {
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
    public function testFindWithNode() {
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
    public function testFindWithNodeList() {
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
    public function testFindWithExpressionThatReturnsScalarExpectingException() {
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
    public function testFindWithInvalidSelectorExpectingException() {
      $fd = new Nodes(self::XML);
      $this->expectException(
        \InvalidArgumentException::class,
        'Invalid selector/expression.'
      );
      $fd->find('');
    }
  }
}
