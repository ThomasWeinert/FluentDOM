<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class InvalidArgumentTest extends TestCase {

    /**
     * @covers \FluentDOM\Exceptions\InvalidArgument
     */
    public function testConstructor(): void {
      $exception = new InvalidArgument('test');
      $this->assertEquals('Invalid $test argument.', $exception->getMessage());
    }

    /**
     * @covers \FluentDOM\Exceptions\InvalidArgument
     */
    public function testConstructorWithSingleType(): void {
      $exception = new InvalidArgument('test', 'int');
      $this->assertEquals('Invalid $test argument. Expected: int', $exception->getMessage());
    }

    /**
     * @covers \FluentDOM\Exceptions\InvalidArgument
     */
    public function testConstructorWithArrayOfTypes(): void {
      $exception = new InvalidArgument('test', ['int', 'string']);
      $this->assertEquals(
        'Invalid $test argument. Expected: int, string', $exception->getMessage()
      );
    }
  }
}
