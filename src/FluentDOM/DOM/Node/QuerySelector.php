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

  interface QuerySelector {

    /**
     * @param string $selector
     * @return Element|NULL
     */
    public function querySelector(string $selector): ?Element;

    /**
     * @param string $selector
     * @return \DOMNodeList
     */
    public function querySelectorAll(string $selector):\DOMNodeList;
  }
}
