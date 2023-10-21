<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';


  /**
   * @covers \FluentDOM\Exceptions\UnattachedNode
   */
  class UnattachedNodeTest extends TestCase  {

    public function testConstructor(): void {
      $exception = new UnattachedNode('some/type');
      $this->assertEquals(
        'Node has no owner document and isn\'t a document.',
        $exception->getMessage()
      );
    }
  }
}
