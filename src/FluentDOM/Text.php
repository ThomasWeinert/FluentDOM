<?php
/**
 * FluentDOM\Text extends PHPs DOMText class.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\Text extends PHPs DOMText class.
   *
   * @property Document $ownerDocument
   */
  class Text
    extends \DOMText  {

    use Node\StringCast;
    use Node\Xpath;
  }
}