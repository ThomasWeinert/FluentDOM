<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class AttributeTest extends TestCase {

    /**
     * @covers FluentDOM\Attribute::__toString
     */
    public function testMagicMethodToString() {
      $dom = new Document();
      $dom->appendElement('test', array('attr' => 'success'));
      $this->assertEquals(
        'success',
        (string)$dom->documentElement->attributes->getNamedItem('attr')
      );
    }
  }
}