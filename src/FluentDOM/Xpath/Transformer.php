<?php
/**
 * Interface for objects that convert a (css) selector string into an XPath expression
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2015 FluentDOM Contributors
 */

namespace FluentDOM\Xpath {

  /**
   * Interface for objects that convert a (css) selector string into an XPath expression for objects that provide an xpath expression when cast to string
   */
  interface Transformer {

    const CONTEXT_CHILDREN = 0;
    const CONTEXT_DOCUMENT = 1;
    const CONTEXT_SELF = 2;

    public function toXpath(string $selector, int $contextMode = self::CONTEXT_CHILDREN, bool $isHtml = FALSE);
  }
}