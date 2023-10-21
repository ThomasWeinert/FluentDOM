<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\Exceptions\ReadOnlyPropertyError;

  require_once __DIR__.'/TestCase.php';

  class QueryTest extends TestCase {

    /**
     * @group Properties
     * @covers \FluentDOM\Query::__isset
     * @dataProvider providePropertyNames
     * @param string $propertyName
     */
    public function testIssetPropertyContentType(string $propertyName): void {
      $fd = new Query();
      $this->assertTrue(isset($fd->$propertyName));
    }

    /**
     * @group Properties
     * @covers \FluentDOM\Query::__unset
     * @dataProvider providePropertyNames
     * @param string $propertyName
     */
    public function testUnsetProperty(string $propertyName): void {
      $fd = new Query();
      $this->expectException(ReadOnlyPropertyError::class);
      unset($fd->$propertyName);
    }

    public static function providePropertyNames(): array {
      return [
        ['attr'],
        ['css'],
        ['data'],
        ['contentType']
      ];
    }
  }
}
