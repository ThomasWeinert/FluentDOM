<?php

namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class ProcessingInstructionTest extends TestCase {

    /**
     * @covers \FluentDOM\ProcessingInstruction
     */
    public function testMagicMethodToString() {
      $dom = new Document();
      $dom
        ->appendElement('test')
        ->appendChild(
          $dom->createProcessingInstruction('php', 'echo "Hello World!";')
        );
      $this->assertEquals(
        'echo "Hello World!";',
        (string)$dom->documentElement->childNodes->item(0)
      );
      $this->assertEquals(
        '<test><?php echo "Hello World!";?></test>',
        $dom->saveXML($dom->documentElement)
      );
    }
  }
}