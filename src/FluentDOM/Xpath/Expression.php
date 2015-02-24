<?php
/**
 *  Interface for objects that provide an xpath expression when cast to string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2015 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Xpath {

  /**
   *  Interface for objects that provide an xpath expression when cast to string
   */
  interface Expression {

    const MODE_HTML = 1;
    const MODE_XML = 2;

    const CONTEXT_DOCUMENT = 4;
    const CONTEXT_CHILDREN = 8;

    function __construct($selector, $options = 0);

    function __toString();
  }
}