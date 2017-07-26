<?php
/**
 * FluentDOM\DOM\Attribute extends PHPs DOMAttr class.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\DOM {

  /**
   * FluentDOM\DOM\Attribute extends PHPs DOMAttr class.
   *
   * @property Document $ownerDocument
   */
  class Attribute
    extends \DOMAttr
    implements Node {

    use Node\Xpath;

    /**
     * Casting the element node to string will returns its value
     *
     * @return string
     */
    public function __toString(): string {
      return $this->value;
    }
  }
}