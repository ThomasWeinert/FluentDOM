<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  /**
   * @covers \FluentDOM\Exceptions\UndeclaredPropertyError
   */
  class UndeclaredPropertyErrorTest extends TestCase  {

    public function testConstructorWithClass(): void {
      $exception = new UndeclaredPropertyError('ExampleClass', 'success');
      $this->assertEquals(
        'Undeclared property ExampleClass::$success not available.',
        $exception->getMessage()
      );
    }

    public function testConstructorWithObject(): void {
      $exception = new UndeclaredPropertyError(new \stdClass(), 'success');
      $this->assertEquals(
        'Undeclared property stdClass::$success not available.',
        $exception->getMessage()
      );
    }
  }
}
