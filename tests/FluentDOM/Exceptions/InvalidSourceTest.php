<?php
namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class InvalidSourceTest extends TestCase {

    /**
     * @covers \FluentDOM\Exceptions\InvalidSource
     */
    public function testConstructor() {
      $exception = new InvalidSource('test', 'type/test');
      $this->assertEquals('Can not load string as "type/test".', $exception->getMessage());
    }
  }
}