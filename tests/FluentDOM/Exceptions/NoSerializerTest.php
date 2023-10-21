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
   * @covers \FluentDOM\Exceptions\NoSerializer
   */
  class NoSerializerTest extends TestCase  {

    public function testConstructor(): void {
      $exception = new NoSerializer('some/type');
      $this->assertEquals(
        'No serializer for content type some/type available.',
        $exception->getMessage()
      );
    }
  }
}
