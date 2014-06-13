<?php
/**
 * FluentDOM\Loadable describes an interface for objects that can be appended to a FluentDOM element node.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\Loadable describes an interface for objects that can be appended to a FluentDOM element node.
   */
  interface Appendable {

    /**
     * Validate if the loader supports the given content type
     *
     * @param Element $parentNode
     * @return Element|boolean|NULL
     */
    function appendTo(Element $parentNode);
  }
}