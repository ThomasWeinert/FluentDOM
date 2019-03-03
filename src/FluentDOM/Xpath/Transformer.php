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
