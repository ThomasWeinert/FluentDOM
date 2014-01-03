<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class AttributesTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @covers FluentDOM\Query\Attributes::__construct
     */
    public function testConstructor() {
      $fd = $this->getMock('FluentDOM\Query');
      $attr = new Attributes($fd);
      $this->assertAttributeSame(
        $fd, '_fd', $attr
      );
    }

    /**
     * @covers FluentDOM\Query\Attributes::toArray
     */
    public function testToArray() {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $this->assertEquals(
        array('foo' => 1, 'bar' => 2),
        $attr->toArray()
      );
    }

    /**
     * @covers FluentDOM\Query\Attributes::offsetExists
     */
    public function testOffsetExistsExpectingTrue() {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $this->assertTrue(isset($attr['foo']));
    }

    /**
     * @covers FluentDOM\Query\Attributes::offsetExists
     */
    public function testOffsetExistsExpectingFalse() {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $this->assertFalse(isset($attr['non_existing']));
    }

    /**
     * @covers FluentDOM\Query\Attributes::offsetExists
     */
    public function testOffsetExistsWithoutSelectionExpectingFalse() {
      $fd = $this->getMock('FluentDOM\Query');
      $fd
        ->expects($this->any())
        ->method('offsetExists')
        ->with(0)
        ->will($this->returnValue(FALSE));
      $attr = new Attributes($fd);
      $this->assertFalse(isset($attr['foo']));
    }

    /**
     * @covers FluentDOM\Query\Attributes::offsetGet
     */
    public function testOffsetGet() {
      $fd = $this->getMock('FluentDOM\Query');
      $fd
        ->expects($this->once())
        ->method('attr')
        ->with('name')
        ->will($this->returnValue('success'));
      $attr = new Attributes($fd);
      $this->assertEquals('success', $attr['name']);
    }

    /**
     * @covers FluentDOM\Query\Attributes::offsetSet
     */
    public function testOffsetSet() {
      $fd = $this->getMock('FluentDOM\Query');
      $fd
        ->expects($this->once())
        ->method('attr')
        ->with('name', 'success');
      $attr = new Attributes($fd);
      $attr['name'] = 'success';
    }

    /**
     * @covers FluentDOM\Query\Attributes::offsetUnset
     */
    public function testOffsetUnset() {
      $fd = $this->getMock('FluentDOM\Query');
      $fd
        ->expects($this->once())
        ->method('removeAttr')
        ->with('name');
      $attr = new Attributes($fd);
      unset($attr['name']);
    }

    /**
     * @covers FluentDOM\Query\Attributes::count
     */
    public function testCountExpectingTwo() {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $this->assertEquals(
        2, count($attr)
      );
    }

    /**
     * @covers FluentDOM\Query\Attributes::count
     */
    public function testCountExpectingZero() {
      $fd = $this->getMock('FluentDOM\Query');
      $fd
        ->expects($this->any())
        ->method('offsetExists')
        ->with(0)
        ->will($this->returnValue(FALSE));
      $attr = new Attributes($fd);
      $this->assertEquals(
        0, count($attr)
      );
    }

    /**
     * @covers FluentDOM\Query\Attributes::getIterator
     */
    public function testGetIterator() {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $iterator = $attr->getIterator();
      $this->assertEquals(
        array('foo' => 1, 'bar' => 2),
        iterator_to_array($iterator)
      );
    }

    /********************
     * Fixtures
     ********************/

    public function getFluentDOMWithNodeFixture($node) {
      $fd = $this->getMock('FluentDOM\Query');
      $fd
        ->expects($this->any())
        ->method('offsetExists')
        ->with(0)
        ->will($this->returnValue(TRUE));
      $fd
        ->expects($this->any())
        ->method('offsetGet')
        ->with(0)
        ->will($this->returnValue($node));
      return $fd;
    }

    public function getSimpleDocumentNodeFixture() {
      $dom = new \DOMDocument;
      $node = $dom->createElement('sample');
      $node->setAttribute('foo', 1);
      $node->setAttribute('bar', 2);
      $dom->appendChild($node);
      return $node;
    }

  }
}