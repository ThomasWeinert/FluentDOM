<?php
namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class JsonTest extends TestCase {

    /**
     * @covers \FluentDOM\Exceptions\JsonError
     */
    public function testWithValidErrorCode() {
      $exception = new Json(1);
      $this->assertEquals(1, $exception->getCode());
      $this->assertEquals('The maximum stack depth has been exceeded', $exception->getMessage());
    }

    /**
     * @covers \FluentDOM\Exceptions\JsonError
     */
    public function testWithInvalidErrorCodeExpectingUnknonwError() {
      $exception = new Json(-42);
      $this->assertEquals(-42, $exception->getCode());
      $this->assertEquals('Unknown error has occurred', $exception->getMessage());
    }
  }
}