<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Element;

  /**
   * Interface ParentNode
   * @property-read Element $firstElementChild
   * @property-read Element $lastElementChild
   * @property-read int $childElementCount
   */
  interface ParentNode extends QuerySelector {

    public function prepend($nodes);

    public function append($nodes);
  }
}
