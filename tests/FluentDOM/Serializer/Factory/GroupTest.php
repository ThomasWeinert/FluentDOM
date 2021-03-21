<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Serializer\Factory {

  use FluentDOM\DOM\Document;
  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\Exceptions\InvalidSerializer;
  use FluentDOM\Serializer\Factory;
  use FluentDOM\TestCase;
  use FluentDOM\Utility\StringCastable;

  require_once __DIR__ . '/../../TestCase.php';

  class GroupTest extends TestCase {

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testConstructor(): void {
      $group = new Group([]);
      $this->assertCount(0, $group);
    }

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testConstructorWithOneFactory(): void {
      $factory = $this->getMockBuilder(Factory::class)->getMock();
      $group = new Group(['type' => $factory]);
      $this->assertCount(1, $group);
      $this->assertSame($factory, $group['type']);
    }

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testFactoryGetAfterSet(): void {
      $factory = $this->getMockBuilder(Factory::class)->getMock();
      $group = new Group();
      $group['type'] = $factory;
      $this->assertTrue(isset($group));
      $this->assertSame($factory, $group['type']);
    }

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testFactorSetWithInvalidFactoryExpectingException(): void {
      $group = new Group();
      $this->expectException(InvalidArgument::class);
      $group['type'] = 'INVALID';
    }

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testFactoryGetAfterRemove(): void {
      $factory = $this->getMockBuilder(Factory::class)->getMock();
      $group = new Group(['type' => $factory]);
      unset($group['type']);
      $this->assertFalse(isset($group['type']));
    }

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testGetIterator(): void {
      $factory = $this->getMockBuilder(Factory::class)->getMock();
      $group = new Group(['type' => $factory]);
      $this->assertSame(['type' => $factory], iterator_to_array($group));
    }

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testCreateSerializer(): void {
      $document = new Document();
      $document->appendElement('dummy');
      $serializer = $this
        ->getMockBuilder(\stdClass::class)
        ->addMethods(['__toString'])
        ->getMock();
      $serializer
        ->method('__toString')
        ->willReturn('success');
      $factory = $this
        ->getMockBuilder(Factory::class)
        ->getMock();
      $factory
        ->expects($this->once())
        ->method('createSerializer')
        ->willReturn($serializer);
      $group = new Group(['some/type' => $factory]);
      $this->assertSame(
        'success',
        (string)$group->createSerializer('some/type', $document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testCreateSerializerWithStringCastable(): void {
      $document = new Document();
      $document->appendElement('dummy');
      $serializer = $this
        ->createMock(StringCastable::class);
      $factory = $this
        ->getMockBuilder(Factory::class)
        ->getMock();
      $factory
        ->expects($this->once())
        ->method('createSerializer')
        ->willReturn($serializer);
      $group = new Group(['some/type' => $factory]);
      $this->assertSame(
        $serializer,
        $group->createSerializer('some/type', $document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Serializer\Factory\Group
     */
    public function testCreateSerializerExpectingException(): void {
      $document = new Document();
      $document->appendElement('dummy');
      $serializer = $this
        ->getMockBuilder(\stdClass::class)
        ->getMock();
      $factory = $this
        ->getMockBuilder(Factory::class)
        ->getMock();
      $factory
        ->expects($this->once())
        ->method('createSerializer')
        ->willReturn($serializer);
      $group = new Group(['some/type' => $factory]);
      $this->expectException(InvalidSerializer::class);
      $group->createSerializer('some/type', $document->documentElement);
    }

  }

}


