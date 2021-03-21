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

  class CommentTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Comment
     */
    public function testMagicMethodToString(): void {
      $document = new Document();
      $document->appendElement('test')->appendChild($document->createComment('success'));
      $this->assertEquals(
        'success',
        (string)$document->documentElement->childNodes->item(0)
      );
      $this->assertEquals(
        '<test><!--success--></test>',
        $document->saveXML($document->documentElement)
      );
    }
  }
}
