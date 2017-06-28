<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class ProcessingInstructionTest extends TestCase {

    /**
     * @covers \FluentDOM\ProcessingInstruction
     */
    public function testMagicMethodToString() {
      $document = new Document();
      $document
        ->appendElement('test')
        ->appendChild(
          $document->createProcessingInstruction('php', 'echo "Hello World!";')
        );
      $this->assertEquals(
        'echo "Hello World!";',
        (string)$document->documentElement->childNodes->item(0)
      );
      $this->assertEquals(
        '<test><?php echo "Hello World!";?></test>',
        $document->saveXML($document->documentElement)
      );
    }
  }
}