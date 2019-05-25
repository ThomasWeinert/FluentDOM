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

    /**
     * The 'Clark Notation' for the attribute.
     *
     * @see http://www.jclark.com/xml/xmlns.htm
     */
    public function clarkNotation() : string {
      if (!$this->namespaceURI) {
        return $this->localName;
      }
      return sprintf('{%s}%s', $this->namespaceURI, $this->localName);
    }
  }
}
