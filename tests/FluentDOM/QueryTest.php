<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  require_once __DIR__.'/TestCase.php';

  class QueryTest extends TestCase {

    /**
     * @group Properties
     * @covers \FluentDOM\Query::__isset
     * @dataProvider providePropertyNames
     */
    public function testIssetPropertyContentType($propertyName): void {
      $fd = new Query();
      $this->assertTrue(isset($fd->$propertyName));
    }

    /**
     * @group Properties
     * @covers \FluentDOM\Query::__unset
     * @dataProvider providePropertyNames
     */
    public function testUnsetPropertyContentType($propertyName): void {
      $fd = new Query();
      $this->expectException(
        \BadMethodCallException::class,
        'Can not unset property FluentDOM\Query::$'.$propertyName
      );
      unset($fd->$propertyName);
    }

    public static function providePropertyNames() {
      return [
        ['attr'],
        ['css'],
        ['data'],
        ['contentType']
      ];
    }
  }
}
