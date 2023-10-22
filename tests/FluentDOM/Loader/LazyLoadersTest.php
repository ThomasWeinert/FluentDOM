<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Loadable;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class LazyLoadersTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testSupportsCallableExpectingTrue(): void {
      $loader = $this->getLoaderFixture();
      $this->assertTrue($loader->supports('callable'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testSupportsLoaderInstanceExpectingTrue(): void {
      $loader = $this->getLoaderFixture();
      $this->assertTrue($loader->supports('loader'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testSupportsWithInvalidTypeExpectingFalse(): void {
      $loader = $this->getLoaderFixture();
      $this->assertFalse($loader->supports('non-existing'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testGetWithCallable(): void {
      $loader = $this->getLoaderFixture();
      $this->assertInstanceOf(Loadable::class, $loader->get('callable'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testGetWithCallableThatDoesNotReturnALoadableExpectingException(): void {
      $this->expectException(\UnexpectedValueException::class);
      $loader = new LazyLoaders(
        [
          'type' => function() { return FALSE; }
        ]
      );
      $this->assertInstanceOf(Loadable::class, $loader->get('type'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testAddClassesWithSingleType(): void {
      $loader = new LazyLoaders();
      $loader->addClasses(
        [
          'XmlLoader' => 'test/unittest'
        ],
        __NAMESPACE__
      );
      $this->assertInstanceOf(XmlLoader::class, $loader->get('test/unittest'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testAddClassesWithSingleTypeExpectingException(): void {
      $loader = new LazyLoaders();
      $loader->addClasses(
        [
          'NonExistingClassName' => 'test/unittest'
        ],
        __NAMESPACE__
      );
      $this->expectException(\LogicException::class);
      $this->expectExceptionMessage('Loader class "FluentDOM\Loader\NonExistingClassName" not found.');
      $this->assertInstanceOf(XmlLoader::class, $loader->get('test/unittest'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testAddClassesWithSeveralTypes(): void {
      $loader = new LazyLoaders();
      $loader->addClasses(
        [
          'XmlLoader' => ['test/unittest', 'test']
        ],
        __NAMESPACE__
      );
      $this->assertInstanceOf(XmlLoader::class, $loader->get('test'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testGetWithLoader(): void {
      $loader = $this->getLoaderFixture();
      $this->assertInstanceOf(Loadable::class, $loader->get('loader'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testAddWithInvalidLoaderExpectingException(): void {
      $this->expectException(\TypeError::class);
      new LazyLoaders(
        [
          'type' => new \stdClass()
        ]
      );
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testLoad(): void {
      $result = $this->createMock(LoaderResult::class);
      $loaderMock = $this->createMock(Loadable::class);
      $loaderMock
        ->expects($this->once())
        ->method('load')
        ->with('data', 'type')
        ->willReturn($result);
      $loader = new LazyLoaders(
        [
          'type' => function() use ($loaderMock) {
            return $loaderMock;
          }
        ]
      );
      $this->assertSame($result, $loader->load('data', 'type'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testLoadWithUnsupportedTypeExpectingNull(): void {
      $loader = $this->getLoaderFixture();
      $this->assertNull($loader->load('', 'non-existing'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testLoadFragment(): void {
      $document = new Document();
      $loaderMock = $this->createMock(Loadable::class);
      $loaderMock
        ->expects($this->once())
        ->method('loadFragment')
        ->with('data', 'type', [])
        ->willReturn($document->createDocumentFragment());
      $loader = new LazyLoaders(
        [
          'type' => function() use ($loaderMock) {
            return $loaderMock;
          }
        ]
      );
      $this->assertInstanceOf(DocumentFragment::class, $loader->loadFragment('data', 'type'));
    }

    /**
     * @covers \FluentDOM\Loader\LazyLoaders
     */
    public function testLoadFragmentWithUnsupportedTypeExpectingNul(): void {
      $loader = new LazyLoaders();
      $this->assertNull($loader->loadFragment('', 'non-existing'));
    }

    private function getLoaderFixture(): LazyLoaders {
      $loaderMock = $this->createMock(Loadable::class);
      return new LazyLoaders(
        [
          'callable' => function() use ($loaderMock) {
            return $loaderMock;
          },
          'loader' => $loaderMock
        ]
      );
    }

  }
}
