<?php
/**
 * A trait that used the serializes an XmlAppendable into a string.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */
namespace FluentDOM {

  /**
   * FluentDOM\Loadable describes an interface for objects that can be serialized to
   * and XML fragment (without document element and declaration).
   */
  trait XmlSerialize {

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
            get_class($this)
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