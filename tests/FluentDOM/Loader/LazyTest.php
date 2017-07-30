<?php
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
    public function testSupportsCallableExpectingTrue() {
      $loader = $this->getLoaderFixture();
      $this->assertTrue($loader->supports('callable'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testSupportsLoaderInstanceExpectingTrue() {
      $loader = $this->getLoaderFixture();
      $this->assertTrue($loader->supports('loader'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testSupportsWithInvalidTypeExpectingFalse() {
      $loader = $this->getLoaderFixture();
      $this->assertFalse($loader->supports('non-existing'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testGetWithCallable() {
      $loader = $this->getLoaderFixture();
      $this->assertInstanceOf(Loadable::class, $loader->get('callable'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testGetWithCallableThatDoesNotReturnALoadableExpectingException() {
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
    public function testAddClassesWithSingleType() {
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
    public function testAddClassesWithSingleTypeExpectingException() {
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
    public function testAddClassesWithSeveralTypes() {
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
    public function testGetWithLoader() {
      $loader = $this->getLoaderFixture();
      $this->assertInstanceOf(Loadable::class, $loader->get('loader'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testAddWithInvalidLoaderExpectingException() {
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
    public function testLoad() {
      $loaderMock = $this->getMockBuilder(Loadable::class)->getMock();
      $loaderMock
        ->expects($this->once())
        ->method('load')
        ->with('data', 'type')
        ->will($this->returnValue(new Document()));
      $loader = new Lazy(
        [
          'type' => function() use ($loaderMock) {
            return $loaderMock;
          }
        ]
      );
      $this->assertInstanceOf(Document::class, $loader->load('data', 'type'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testLoadWithUnsupportedTypeExpectingNull() {
      $loader = $this->getLoaderFixture();
      $this->assertNull($loader->load('', 'non-existing'));
    }

    /**
     * @covers \FluentDOM\Loader\Lazy
     */
    public function testLoadFragment() {
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
    public function testLoadFragmentWithUnsupportedTypeExpectingNul() {
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