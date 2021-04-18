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

  class ProcessingInstructionTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\ProcessingInstruction
     */
    public function testMagicMethodToString(): void {
      $document = new Document();
      $document
        ->appendElement('test')
        ->appendChild(
          $document->createProcessingInstruction('php', 'echo "Hello World!";')
        );
      /** @var ProcessingInstruction $node */
      $node = $document->documentElement->childNodes->item(0);
      $this->assertEquals('echo "Hello World!";', (string)$node);
      $this->assertEquals(
        '<test><?php echo "Hello World!";?></test>',
        $document->saveXML($document->documentElement)
      );
    }
  }
}
