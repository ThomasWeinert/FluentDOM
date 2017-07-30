<?php
namespace FluentDOM\Exceptions\InvalidSource {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class VariableTest extends TestCase {

    /**
     * @covers \FluentDOM\Exceptions\InvalidSource\Variable
     */
    public function testConstructor() {
      $exception = new Variable('test', 'type/test');
      $this->assertEquals('Can not load string as "type/test".', $exception->getMessage());
    }
  }
}