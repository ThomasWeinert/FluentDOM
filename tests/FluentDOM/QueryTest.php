<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class QueryTest extends TestCase {

    /**
     * @group MagicFunctions
     * @covers FluentDOM\Query::__call()
     */
    public function testMagicMethodCallWithUnknownMethodExpectingException() {
      $fd = new Query();
      $this->setExpectedException('BadMethodCallException');
      /** @noinspection PhpUndefinedMethodInspection */
      $fd->invalidMethodCall();
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__isset
     * @dataProvider providePropertyNames
     */
    public function testIssetPropertyContentType($propertyName) {
      $fd = new Query();
      $this->assertTrue(isset($fd->$propertyName));
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__isset
     * @dataProvider providePropertyNames
     */
    public function testUnsetPropertyContentType($propertyName) {
      $fd = new Query();
      $this->setExpectedException(
        'BadMethodCallException',
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