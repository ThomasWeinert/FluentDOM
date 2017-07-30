<?php
/**
 * Standard implementation for FluentDOM\XmlSerializable
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;

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
    public function getXml(): string {
      if (!$this instanceOf Appendable) {
        throw new \LogicException(
          sprintf(
            'Class %s does not implement the FluentDOM\Appendable interface.',
            is_object($this) ? get_class($this) : ''
          )
        );
      }
      $document = new Document();
      $fragment = $document->appendElement('fragment');
      $this->appendTo($fragment);
      $xml = '';
      foreach ($document->documentElement->childNodes as $node) {
        $xml .= $node->ownerDocument->saveXml($node);
      }
      return $xml;
    }
  }
}