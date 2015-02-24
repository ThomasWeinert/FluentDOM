<?php
/**
 * Interface for objects that convert a (css) selector string into an XPath expression
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2015 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Xpath {

  /**
   * Interface for objects that convert a (css) selector string into an XPath expression for objects that provide an xpath expression when cast to string
   */
  interface Transformer {

    function toXpath($selector, $isDocumentContext = FALSE, $isHtml = FALSE);
  }
}