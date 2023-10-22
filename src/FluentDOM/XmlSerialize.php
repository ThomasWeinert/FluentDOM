<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;

  /**
   * StandardLoaders implementation for FluentDOM\XmlSerializable
   */
  trait XmlSerialize {

    abstract public function appendTo(Element $parentNode): void;

    /**
     * Return the object as an XML fragment.
     *
     * @return string
     * @throws \DOMException
     * @throws \LogicException
     */
    public function getXml(): string {
      if (!$this instanceOf Appendable) {
        throw new \LogicException(
          \sprintf(
            'Class %s does not implement the FluentDOM\Appendable interface.',
            \is_object($this) ? \get_class($this) : ''
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
