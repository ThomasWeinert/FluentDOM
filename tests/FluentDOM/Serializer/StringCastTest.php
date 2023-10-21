<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Serializer {

  use FluentDOM\DOM\Document;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  /**
   * @covers \FluentDOM\Serializer\StringCast
   */
  class StringCastTest extends TestCase  {

    public function testToString(): void {
      $serializer = new StringCast(
        new class {
          public function __toString(): string {
            return 'success';
          }
        }
      );
      $this->assertEquals(
        'success', (string)$serializer
      );
    }
  }
}
