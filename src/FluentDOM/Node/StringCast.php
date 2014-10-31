<?php
/**
 * Cast a DOMNode into a string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Node {

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
    public function __toString() {
      /** @var \DOMNode $this */
      return ($this instanceof \DOMNode) ? (string)$this->nodeValue : '';
    }
  }
}