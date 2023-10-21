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
   * @covers \FluentDOM\Exceptions\ReadOnlyPropertyError
   */
  class ReadOnlyPropertyErrorTest extends TestCase  {

    public function testConstructorWithClass(): void {
      $exception = new ReadOnlyPropertyError('ExampleClass', 'success');
      $this->assertEquals(
        'Can not write read only property ExampleClass::$success.',
        $exception->getMessage()
      );
    }

    public function testConstructorWithObject(): void {
      $exception = new ReadOnlyPropertyError(new \stdClass(), 'success');
      $this->assertEquals(
        'Can not write read only property stdClass::$success.',
        $exception->getMessage()
      );
    }
  }
}
