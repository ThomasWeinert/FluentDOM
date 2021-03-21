<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\Loader\Result;

  require_once __DIR__.'/TestCase.php';

  class LoadersTest extends TestCase {

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testConstructorWithTwoLoaders(): void {
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
    public function testAdd(): void {
      $loaders = new Loaders();
      $loaders->add(
        $loader = $this->getMockBuilder(Loadable::class)->getMock()
      );
      $this->assertSame([$loader], iterator_to_array($loaders));
    }

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testRemove(): void {
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
    public function testSupportsExpectingTrue(): void {
      $loader = $this->getMockBuilder(Loadable::class)->getMock();
      $loader
        ->expects($this->once())
        ->method('supports')
        ->with('application/json')
        ->willReturn(TRUE);
      $loaders = new Loaders([$loader]);
      $this->assertTrue($loaders->supports('application/json'));
    }

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testSupportsExpectingFalse(): void {
      $loader = $this->getMockBuilder(Loadable::class)->getMock();
      $loader
        ->expects($this->once())
        ->method('supports')
        ->with('application/json')
        ->willReturn(FALSE);
      $loaders = new Loaders([$loader]);
      $this->assertFalse($loaders->supports('application/json'));
    }

    /**
     * @covers \FluentDOM\Loaders
     */
    public function testLoadUsesSecondLoader(): void {
      $result = $this->createMock(Result::class);
      $loaderOne = $this->createMock(Loadable::class);
      $loaderOne
        ->expects($this->once())
        ->method('supports')
        ->with('text/xml')
        ->willReturn(FALSE);
      $loaderTwo = $this->createMock(Loadable::class);
      $loaderTwo
        ->expects($this->once())
        ->method('supports')
        ->with('text/xml')
        ->willReturn(TRUE);
      $loaderTwo
        ->expects($this->once())
        ->method('load')
        ->with('DATA', 'text/xml')
        ->willReturn($result);
      $loaders = new Loaders([$loaderOne, $loaderTwo]);
      $this->assertSame($result, $loaders->load('DATA', 'text/xml'));
    }
  }
}
