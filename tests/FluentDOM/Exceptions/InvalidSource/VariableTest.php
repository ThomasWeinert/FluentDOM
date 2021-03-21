<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions\InvalidSource {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class VariableTest extends TestCase {

    /**
     * @covers \FluentDOM\Exceptions\InvalidSource\Variable
     */
    public function testConstructor(): void {
      $exception = new Variable('test', 'type/test');
      $this->assertEquals('Can not load string as "type/test".', $exception->getMessage());
    }
  }
}
