<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingFindTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     * @covers FluentDOM\Query::getNodes
     */
    public function testFind() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = $fd->find('group/item');
      $this->assertEquals(3, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     * @covers FluentDOM\Query::getNodes
     */
    public function testFindWithSelectorCallback() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $fd->onPrepareSelector = function() {return '//item'; };
      $fd = $fd->find('/*');
      $this->assertEquals(3, $fd->length);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     * @covers FluentDOM\Query::getNodes
     */
    public function testFindFromRootNode() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('/*');
      $this->assertEquals(1, $fd->length);
      $findFd = $this->getQueryFixtureFromString(self::XML)->find('/items');
      $this->assertEquals(1, $findFd->length);
      $this->assertTrue($findFd !== $fd);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     * @covers FluentDOM\Query::getNodes
     */
    public function testFindWithNode() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $fd = $fd->find($fd->document->documentElement);
      $this->assertEquals('items', $fd->item(0)->nodeName);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     * @covers FluentDOM\Query::getNodes
     */
    public function testFindWithNodeList() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $fd = $fd->find([$fd->document->documentElement]);
      $this->assertEquals('items', $fd->item(0)->nodeName);
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     * @covers FluentDOM\Query::getNodes
     */
    public function testFindWithExpressionThatReturnsScalarExpectingException() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $this->setExpectedException(
        'InvalidArgumentException',
        'Given selector did not return an node list.'
      );
      $fd->find('count(*)');
    }

    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::find
     * @covers FluentDOM\Query::getNodes
     */
    public function testFindWithInvalidSelectorExpectingException() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $this->setExpectedException('InvalidArgumentException', 'Invalid selector');
      $fd->find(NULL);
    }
  }
}
