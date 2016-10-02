<?php
/**
 * Serialize a DOM into an Xml string.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2016 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer {

  class Xml {
    /**
     * @var \DOMNode
     */
    protected $_node = NULL;

    /**
     * @param \DOMNode $node
     */
    public function __construct(\DOMNode $node) {
      $this->_node = $node;
    }

    /**
     * @return string
     */
    public function __toString() {
      return $this->_node instanceof \DOMDocument
        ? $this->_node->saveXml()
        : $this->_node->ownerDocument->saveXml($this->_node);
    }
  }
}