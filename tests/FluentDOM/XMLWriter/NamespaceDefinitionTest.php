<?php
namespace FluentDOM\XMLWriter {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class NamespaceDefinitionTest extends TestCase {

    /**
     * @covers \FluentDOM\XMLWriter\NamespaceDefinition
     */
    public function testDecreaseExpectingException() {
      $definition = new NamespaceDefinition();
      $this->expectException(\LogicException::class);
      $definition->decreaseDepth();
    }
  }
}