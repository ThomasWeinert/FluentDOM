<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Node;

  interface ChildNode extends Node {

    public function remove():void;
    public function before(...$nodes): void;
    public function after(...$nodes): void;
    public function replaceWith(...$nodes):void;
  }
}
