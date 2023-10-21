<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  /**
   * @covers \FluentDOM\Exceptions\Json
   */
  class JsonTest extends TestCase {

    /**
     * @covers \FluentDOM\Exceptions\LoadingError\Json
     */
    public function testWithValidErrorCode(): void {
      $exception = new Json(1);
      $this->assertEquals(1, $exception->getCode());
      $this->assertEquals('The maximum stack depth has been exceeded', $exception->getMessage());
    }

    /**
     * @covers \FluentDOM\Exceptions\LoadingError\Json
     */
    public function testWithInvalidErrorCodeExpectingUnknownError(): void {
      $exception = new Json(-42);
      $this->assertEquals(-42, $exception->getCode());
      $this->assertEquals('Unknown error has occurred', $exception->getMessage());
    }
  }
}
