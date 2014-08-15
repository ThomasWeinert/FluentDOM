<?php
/**
 * Allow an object to be appendable to a FluentDOM\Element
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * Allow an object to be appendable to a FluentDOM\Element
   */
  interface Appendable {

    /**
     * Append the object to a FluentDOM\Element
     *
     * @param Element $parentNode
     * @return Element
     */
    function appendTo(Element $parentNode);
  }
}