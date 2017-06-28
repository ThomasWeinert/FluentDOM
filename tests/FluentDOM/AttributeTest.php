<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class AttributeTest extends TestCase {

    /**
     * @covers \FluentDOM\Attribute::__toString
     */
    public function testMagicMethodToString() {
      $document = new Document();
      $document->appendElement('test', array('attr' => 'success'));
      $this->assertEquals(
        'success',
        (string)$document->documentElement->attributes->getNamedItem('attr')
      );
    }
  }
}