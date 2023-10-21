<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;
  use PHPUnit\Framework\MockObject\MockObject;

  require_once __DIR__.'/../TestCase.php';

  /**
   * @covers \FluentDOM\Query\Attributes
   */
  class AttributesTest extends TestCase {

    protected $_directory = __DIR__;

    public function testConstructor(): void {
      $fd = $this->createMock(Query::class);
      $attr = new Attributes($fd);
      $this->assertSame(
        $fd, $attr->getOwner()
      );
    }

    public function testToArray(): void {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $this->assertEquals(
        ['foo' => 1, 'bar' => 2],
        $attr->toArray()
      );
    }

    public function testOffsetExistsExpectingTrue(): void {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $this->assertTrue(isset($attr['foo']));
    }

    public function testOffsetExistsExpectingFalse(): void {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $this->assertFalse(isset($attr['non_existing']));
    }

    public function testOffsetExistsWithoutSelectionExpectingFalse(): void {
      $fd = $this->createMock(Query::class);
      $fd
        ->method('offsetExists')
        ->with(0)
        ->willReturn(FALSE);
      $attr = new Attributes($fd);
      $this->assertFalse(isset($attr['foo']));
    }

    public function testOffsetGet(): void {
      $fd = $this->createMock(Query::class);
      $fd
        ->expects($this->once())
        ->method('attr')
        ->with('name')
        ->willReturn('success');
      $attr = new Attributes($fd);
      $this->assertEquals('success', $attr['name']);
    }

    public function testOffsetSet(): void {
      $fd = $this->createMock(Query::class);
      $fd
        ->expects($this->once())
        ->method('attr')
        ->with('name', 'success');
      $attr = new Attributes($fd);
      $attr['name'] = 'success';
    }

    public function testOffsetUnset(): void {
      $fd = $this->createMock(Query::class);
      $fd
        ->expects($this->once())
        ->method('removeAttr')
        ->with('name');
      $attr = new Attributes($fd);
      unset($attr['name']);
    }

    public function testCountExpectingTwo(): void {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $this->assertCount(2, $attr);
    }

    public function testCountExpectingZero(): void {
      $fd = $this->createMock(Query::class);
      $fd
        ->method('offsetExists')
        ->with(0)
        ->willReturn(FALSE);
      $attr = new Attributes($fd);
      $this->assertCount(0, $attr);
    }

    public function testGetIterator(): void {
      $fd = $this->getFluentDOMWithNodeFixture(
        $this->getSimpleDocumentNodeFixture()
      );
      $attr = new Attributes($fd);
      $iterator = $attr->getIterator();
      $this->assertEquals(
        ['foo' => 1, 'bar' => 2],
        iterator_to_array($iterator)
      );
    }

    /********************
     * Fixtures
     *******************/

    /**
     * @param \DOMNode $node
     * @return MockObject|Query
     */
    public function getFluentDOMWithNodeFixture(\DOMNode $node) {
      $fd = $this->createMock(Query::class);
      $fd
        ->method('offsetExists')
        ->with(0)
        ->willReturn(TRUE);
      $fd
        ->method('offsetGet')
        ->with(0)
        ->willReturn($node);
      return $fd;
    }

    public function getSimpleDocumentNodeFixture(): \DOMElement {
      $document = new \DOMDocument;
      $node = $document->createElement('sample');
      $node->setAttribute('foo', 1);
      $node->setAttribute('bar', 2);
      $document->appendChild($node);
      return $node;
    }

  }
}
