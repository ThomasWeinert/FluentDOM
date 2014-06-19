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
  }
}