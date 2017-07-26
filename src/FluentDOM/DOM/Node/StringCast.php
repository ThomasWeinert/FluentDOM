<?php
/**
 * Cast a DOMNode into a string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\DOM\Node {

  /**
   * Cast a DOMNode into a string
   *
   * @property string $nodeValue
   */
  trait StringCast {

    /**
     * Casting the element node to string will returns its node value
     *
     * @return string
     */
    public function __toString(): string {
      /** @var \DOMNode $this */
      return ($this instanceof \DOMNode) ? (string)$this->textContent : '';
    }
  }
}