<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class QueryTest extends TestCase {

    /**
     * @group Properties
     * @covers \FluentDOM\Query::__isset
     * @dataProvider providePropertyNames
     */
    public function testIssetPropertyContentType($propertyName) {
      $fd = new Query();
      $this->assertTrue(isset($fd->$propertyName));
    }

    /**
     * @group Properties
     * @covers \FluentDOM\Query::__unset
     * @dataProvider providePropertyNames
     */
    public function testUnsetPropertyContentType($propertyName) {
      $fd = new Query();
      $this->expectException(
        \BadMethodCallException::class,
        'Can not unset property FluentDOM\Query::$'.$propertyName
      );
      unset($fd->$propertyName);
    }

    public static function providePropertyNames() {
      return array(
        array('attr'),
        array('css'),
        array('data'),
        array('contentType')
      );
    }
  }
}