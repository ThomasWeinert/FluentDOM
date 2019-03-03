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
     * @return Element|NULL
     */
    public function appendTo(Element $parentNode);
  }
}
