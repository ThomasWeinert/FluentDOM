<?php

namespace FluentDOM\DOM {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class AttributeTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Attribute
     */
    public function testMagicMethodToString() {
      $document = new Document();
      $document->appendElement('test', ['attr' => 'success']);
      $attribute = $document->documentElement->attributes->getNamedItem('attr');
      $this->assertEquals(
        'success',
        (string)$attribute
      );
    }
  }
}