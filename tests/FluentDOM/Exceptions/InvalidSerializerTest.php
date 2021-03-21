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

  class InvalidSerializerTest extends TestCase  {

    public function testConstructor(): void {
      $exception = new InvalidSerializer('some/type', 'SerializerClass');
      $this->assertEquals(
        'Invalid serializer for content type some/type, instances of SerializerClass are not castable to string.',
        $exception->getMessage()
      );
    }
  }
}
