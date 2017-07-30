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
      $this->assertEquals(
        'success',
        (string)$document->documentElement->attributes->getNamedItem('attr')
      );
    }
  }
}