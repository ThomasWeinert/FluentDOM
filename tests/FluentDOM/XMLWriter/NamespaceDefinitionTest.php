<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\XMLWriter {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  class NamespaceDefinitionTest extends TestCase {

    /**
     * @covers \FluentDOM\XMLWriter\NamespaceDefinition
     */
    public function testDecreaseExpectingException(): void {
      $definition = new NamespaceDefinition();
      $this->expectException(\LogicException::class);
      $definition->decreaseDepth();
    }
  }
}
