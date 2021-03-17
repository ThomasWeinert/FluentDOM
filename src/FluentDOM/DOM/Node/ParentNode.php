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

  use FluentDOM\DOM\Element;
  use FluentDOM\DOM\Node;

  /**
   * Interface ParentNode
   * @property-read Element $firstElementChild
   * @property-read Element $lastElementChild
   * @property-read int $childElementCount
   */
  interface ParentNode extends Node, QuerySelector {

    public function prepend(...$nodes): void;

    public function append(...$nodes): void;
  }
}
