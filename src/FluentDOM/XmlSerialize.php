<?php
/**
 * Standard implementation for FluentDOM\XmlSerializable
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * Standard implementation for FluentDOM\XmlSerializable
   */
  trait XmlSerialize {

    abstract public function appendTo(Element $parentNode);

    /**
     * Return the object as an XML fragment.
     *
     * @throws \LogicException
     * @return string
     */
    public function getXml() {
      if (!$this instanceOf Appendable) {
        throw new \LogicException(
          sprintf(
            'Class %s does not implement the FluentDOM\Appendable interface.',
            is_object($this) ? get_class($this) : ''
          )
        );
      }
      $dom = new Document();
      $fragment = $dom->appendElement('fragment');
      $this->appendTo($fragment);
      $xml = '';
      foreach ($dom->documentElement->childNodes as $node) {
        $xml .= $node->ownerDocument->saveXml($node);
      }
      return $xml;
    }
  }
}