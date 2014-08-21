<?php
/**
 * FluentDOM\Attribute extends PHPs DOMAttr class.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\Attribute extends PHPs DOMAttr class.
   *
   * @property Document $ownerDocument
   */
  class Attribute
    extends \DOMAttr  {

    use Node\Xpath;

    /**
     * Casting the element node to string will returns its value
     *
     * @return string
     */
    public function __toString() {
      return $this->value;
    }
  }
}