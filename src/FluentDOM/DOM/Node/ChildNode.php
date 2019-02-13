<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\DOM\Node {

  use FluentDOM\DOM\Node;

  interface ChildNode extends Node {

    public function remove():\DOMNode;
    public function before($nodes);
    public function after($nodes);
    public function replace($nodes):\DOMNode;
  }
}
