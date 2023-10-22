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

namespace FluentDOM\Serializer {

  class XmlSerializer implements Serializer {


    protected \DOMNode $_node;

    public function __construct(\DOMNode $node) {
      $this->_node = $node;
    }

    /**
     * @return string
     */
    public function __toString(): string {
      return $this->_node instanceof \DOMDocument
        ? $this->_node->saveXML()
        : $this->_node->ownerDocument->saveXML($this->_node);
    }
  }
}