<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class LoadersTest extends TestCase {

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testConstructorWithTwoLoaders() {
      $loaders = new Loaders(
        $list = [
          $this->getMockBuilder(Loadable::class)->getMock(),
          $this->getMockBuilder(Loadable::class)->getMock()
        ]
      );
      $this->assertSame($list, iterator_to_array($loaders));
    }

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testAdd() {
      $loaders = new Loaders();
      $loaders->add(
        $loader = $this->getMockBuilder(Loadable::class)->getMock()
      );
      $this->assertSame([$loader], iterator_to_array($loaders));
    }

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testRemove() {
      $loaders = new Loaders(
        [
          $loaderOne = $this->getMockBuilder(Loadable::class)->getMock(),
          $loaderTwo = $this->getMockBuilder(Loadable::class)->getMock()
        ]
      );
      $loaders->remove($loaderOne);
      $this->assertSame([$loaderTwo], iterator_to_array($loaders));
    }

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testSupportsExpectingTrue() {
      $loader = $this->getMockBuilder(Loadable::class)->getMock();
      $loader
        ->expects($this->once())
        ->method('supports')
        ->with('application/json')
        ->will($this->returnValue(TRUE));
      $loaders = new Loaders([$loader]);
      $this->assertTrue($loaders->supports('application/json'));
    }

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testSupportsExpectingFalse() {
      $loader = $this->getMockBuilder(Loadable::class)->getMock();
      $loader
        ->expects($this->once())
        ->method('supports')
        ->with('application/json')
        ->will($this->returnValue(FALSE));
      $loaders = new Loaders([$loader]);
      $this->assertFalse($loaders->supports('application/json'));
    }

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testLoadUsesSecondLoader() {
      $loaderOne = $this->getMockBuilder(Loadable::class)->getMock();
      $loaderOne
        ->expects($this->once())
        ->method('supports')
        ->with('text/xml')
        ->will($this->returnValue(FALSE));
      $loaderTwo = $this->getMockBuilder(Loadable::class)->getMock();
      $loaderTwo
        ->expects($this->once())
        ->method('supports')
        ->with('text/xml')
        ->will($this->returnValue(TRUE));
      $loaderTwo
        ->expects($this->once())
        ->method('load')
        ->with('DATA', 'text/xml')
        ->will($this->returnValue($dom = new \DOMDOcument));
      $loaders = new Loaders([$loaderOne, $loaderTwo]);
      $this->assertSame($dom, $loaders->load('DATA', 'text/xml'));
    }
  }
}
