<?php
/**
 * FluentDOM\XmlSerializable describes an interface for objects that can be serialized to
 * and XML fragment (without document element and declaration).
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM {

  /**
   * FluentDOM\XmlSerializable describes an interface for objects that can be serialized to
   * and XML fragment (without document element and declaration).
   */
  interface XmlSerializable extends Appendable {

    /**
     * Return the object as an XML fragment.
     *
     * @return string
     */
    public function getXml(): string;
  }
}