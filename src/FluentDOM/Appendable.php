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

namespace FluentDOM {

  use FluentDOM\DOM\Element;

  /**
   * Allow an object to be appendable to a FluentDOM\DOM\Element
   */
  interface Appendable {

    /**
     * Append the object to a FluentDOM\DOM\Element
     *
     * @param Element $parentNode
     */
    public function appendTo(Element $parentNode): void;
  }
}
