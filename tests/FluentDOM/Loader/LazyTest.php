<?php
namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class LazyTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testSupportsCallableExpectingTrue() {
      $loader = $this->getLoaderFixture();
      $this->assertTrue($loader->supports('callable'));
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testSupportsLoaderInstanceExpectingTrue() {
      $loader = $this->getLoaderFixture();
      $this->assertTrue($loader->supports('loader'));
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testSupportsWithInvalidTypeExpectingFalse() {
      $loader = $this->getLoaderFixture();
      $this->assertFalse($loader->supports('non-existing'));
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testGetWithCallable() {
      $loader = $this->getLoaderFixture();
      $this->assertInstanceOf('FluentDOM\Loadable', $loader->get('callable'));
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testGetWithCallableThatDoesNotReturnALoadableExpectingException() {
      $this->setExpectedException('UnexpectedValueException');
      $loader = new Lazy(
        [
          'type' => function() { return FALSE; }
        ]
      );
      $this->assertInstanceOf('FluentDOM\Loadable', $loader->get('type'));
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testAddClassesWithSingleType() {
      $loader = new Lazy();
      $loader->addClasses(
        [
          'Xml' => ['test/unittest']
        ],
        __NAMESPACE__
      );
      $this->assertInstanceOf('FluentDOM\Loader\Xml', $loader->get('test/unittest'));
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testAddClassesWithSeveralTypes() {
      $loader = new Lazy();
      $loader->addClasses(
        [
          'Xml' => ['test/unittest', 'test']
        ],
        __NAMESPACE__
      );
      $this->assertInstanceOf('FluentDOM\Loader\Xml', $loader->get('test'));
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testGetWithLoader() {
      $loader = $this->getLoaderFixture();
      $this->assertInstanceOf('FluentDOM\Loadable', $loader->get('loader'));
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testAddWithInvalidLoaderExpectingException() {
      $this->setExpectedException('UnexpectedValueException');
      new Lazy(
        [
          'type' => new \stdClass()
        ]
      );
    }

    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testLoad() {
      $loaderMock = $this->getMock('FluentDOM\Loadable');
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
      $this->assertInstanceOf('FluentDOM\Document', $loader->load('data', 'type'));
    }
    /**
     * @covers FluentDOM\Loader\Lazy
     */
    public function testLoadWithUnsupportedTypeExpectingNull() {
      $loader = $this->getLoaderFixture();
      $this->assertNull($loader->load('', 'non-existing'));
    }

    private function getLoaderFixture() {
      $loaderMock = $this->getMock('FluentDOM\Loadable');
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