<?php
namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class InvalidArgumentTest extends TestCase {

    /**
     * @covers \FluentDOM\Exceptions\InvalidArgument
     */
    public function testConstructor() {
      $exception = new InvalidArgument('test');
      $this->assertEquals('Invalid $test argument.', $exception->getMessage());
    }

    /**
     * @covers \FluentDOM\Exceptions\InvalidArgument
     */
    public function testConstructorWithSingleType() {
      $exception = new InvalidArgument('test', 'int');
      $this->assertEquals('Invalid $test argument. Expected: int', $exception->getMessage());
    }

    /**
     * @covers \FluentDOM\Exceptions\InvalidArgument
     */
    public function testConstructorWithArrayOfTypes() {
      $exception = new InvalidArgument('test', ['int', 'string']);
      $this->assertEquals(
        'Invalid $test argument. Expected: int, string', $exception->getMessage()
      );
    }
  }
}