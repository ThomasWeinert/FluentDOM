<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class EntityReferenceTest extends TestCase {

    /**
     * @covers \FluentDOM\EntityReference
     */
    public function testStringCast() {
      $document = new Document();
      $document->loadXml(
        '<!DOCTYPE p ['."\n".
        '  <!ENTITY entity "foo<br/>">'."\n".
        ']>'."\n".
        '<p>&entity;</p>'
      );
      $entity = $document->documentElement->firstChild;
      $this->assertInstanceOf(EntityReference::class, $entity);
      $this->assertEquals('foo', (string)$entity);
    }
  }

}
