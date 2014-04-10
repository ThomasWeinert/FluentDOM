<?php
/**
 * FluentDOM\Element extends PHPs DOMDocument class. It adds some generic namespace handling on
 * the document level and registers extended Node classes for convenience.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\Element extends PHPs DOMDocument class. It adds some generic namespace handling on
   * the document level and registers extended Node classes for convenience.
   *
   * @property Document $ownerElement
   */
  class Element extends \DOMElement {

    /**
     * Set an attribute on an element
     *
     * @param string $name
     * @param string $value
     * @return \DOMAttr
     */
    public function setAttribute($name, $value) {
      $namespace = '';
      if (
        $this->ownerDocument instanceOf Document &&
        FALSE !== ($position = strpos($name, ':'))
      ) {
        $prefix = substr($name, 0, $position);
        $namespace = $this->ownerDocument->getNamespace($prefix);
      }
      if ($namespace != '') {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return parent::setAttributeNS($namespace, $name, $value);
      } else {
        return parent::setAttribute($name, $value);
      }
    }

    /**
     * Call an object to append itself to the element.
     *
     * @param Appendable $object
     * @return NULL|Element
     */
    public function append(Appendable $object) {
      return $object->appendTo($this);
    }

    /**
     * Append an child element
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return \DOMElement
     */
    public function appendElement($name, $content = '', array $attributes = NULL) {
      $this->appendChild(
        $node = $this->ownerDocument->createElement($name, $content, $attributes)
      );
      return $node;
    }

    /**
     * Append an xml fragment to the element node
     *
     * @param string $xmlFragment
     */
    public function appendXml($xmlFragment) {
      $fragment = $this->ownerDocument->createDocumentFragment();
      $fragment->appendXML($xmlFragment);
      $this->appendChild($fragment);
    }

    /**
     * save the element node as XML
     *
     * @return string
     */
    public function saveXml() {
      return $this->ownerDocument->saveXML($this);
    }

    /**
     * save the element node as HTML
     *
     * @return string
     */
    public function saveHtml() {
      return $this->ownerDocument->saveHTML($this);
    }
  }
}