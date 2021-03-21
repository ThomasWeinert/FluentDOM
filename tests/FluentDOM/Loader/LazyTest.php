<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Loadable;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class LazyTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testSupportsCallableExpectingTrue(): void {
      $loader = $this->getLoaderFixture();
      $this->assertTrue($loader->supports('callable'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testSupportsLoaderInstanceExpectingTrue(): void {
      $loader = $this->getLoaderFixture();
      $this->assertTrue($loader->supports('loader'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testSupportsWithInvalidTypeExpectingFalse(): void {
      $loader = $this->getLoaderFixture();
      $this->assertFalse($loader->supports('non-existing'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testGetWithCallable(): void {
      $loader = $this->getLoaderFixture();
      $this->assertInstanceOf(Loadable::class, $loader->get('callable'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testGetWithCallableThatDoesNotReturnALoadableExpectingException(): void {
      $this->expectException(\UnexpectedValueException::class);
      $loader = new Lazy(
        [
          'type' => function() { return FALSE; }
        ]
      );
      $this->assertInstanceOf(Loadable::class, $loader->get('type'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testAddClassesWithSingleType(): void {
      $loader = new Lazy();
      $loader->addClasses(
        [
          'Xml' => 'test/unittest'
        ],
        __NAMESPACE__
      );
      $this->assertInstanceOf(Xml::class, $loader->get('test/unittest'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testAddClassesWithSingleTypeExpectingException(): void {
      $loader = new Lazy();
      $loader->addClasses(
        [
          'NonExistingClassName' => 'test/unittest'
        ],
        __NAMESPACE__
      );
      $this->expectException(
        \LogicException::class,
        'Loader class "FluentDOM\Loader\NonExistingClassName" not found.'
      );
      $this->assertInstanceOf(Xml::class, $loader->get('test/unittest'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testAddClassesWithSeveralTypes(): void {
      $loader = new Lazy();
      $loader->addClasses(
        [
          'Xml' => ['test/unittest', 'test']
        ],
        __NAMESPACE__
      );
      $this->assertInstanceOf(Xml::class, $loader->get('test'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testGetWithLoader(): void {
      $loader = $this->getLoaderFixture();
      $this->assertInstanceOf(Loadable::class, $loader->get('loader'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testAddWithInvalidLoaderExpectingException(): void {
      $this->expectException(\UnexpectedValueException::class);
      new Lazy(
        [
          'type' => new \stdClass()
        ]
      );
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testLoad(): void {
      $result = $this->createMock(Result::class);
      $loaderMock = $this->createMock(Loadable::class);
      $loaderMock
        ->expects($this->once())
        ->method('load')
        ->with('data', 'type')
        ->willReturn($result);
      $loader = new Lazy(
        [
          'type' => function() use ($loaderMock) {
            return $loaderMock;
          }
        ]
      );
      $this->assertSame($result, $loader->load('data', 'type'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testLoadWithUnsupportedTypeExpectingNull(): void {
      $loader = $this->getLoaderFixture();
      $this->assertNull($loader->load('', 'non-existing'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testLoadFragment(): void {
      $document = new Document();
      $loaderMock = $this->getMockBuilder(Loadable::class)->getMock();
      $loaderMock
        ->expects($this->once())
        ->method('loadFragment')
        ->with('data', 'type', [])
        ->willReturn($document->createDocumentFragment());
      $loader = new Lazy(
        [
          'type' => function() use ($loaderMock) {
            return $loaderMock;
          }
        ]
      );
      $this->assertInstanceOf(DocumentFragment::class, $loader->loadFragment('data', 'type'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testLoadFragmentWithUnsupportedTypeExpectingNul(): void {
      $loader = new Lazy();
      $this->assertNull($loader->loadFragment('', 'non-existing'));
    }

    private function getLoaderFixture() {
      $loaderMock = $this->getMockBuilder(Loadable::class)->getMock();
      $loader = new Lazy(
        [
          'callable' => function() use ($loaderMock) {
            return $loaderMock;
          },
          'loader' => $loaderMock
        ]
      );
      return $loader;
    }

  }
}
