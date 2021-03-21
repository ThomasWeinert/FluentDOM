<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM {

  require_once __DIR__ . '/../TestCase.php';

  use FluentDOM\TestCase;

  class EntityReferenceTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\EntityReference
     */
    public function testStringCast(): void {
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
