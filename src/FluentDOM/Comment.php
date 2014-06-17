<?php
/**
 * FluentDOM\Comment extends PHPs DOMComment class.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\Comment extends PHPs DOMComment class.
   *
   * @property Document $ownerElement
   */
  class Comment
    extends \DOMComment  {

    use Node\StringCast;
  }
}