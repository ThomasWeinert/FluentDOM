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

  class HtmlSerializer implements Serializer {

    protected \DOMNode $_node;

    public function __construct(\DOMNode $node) {
      $this->_node = $node;
    }

    public function __toString(): string {
      return $this->_node instanceof \DOMDocument
        ? $this->_node->saveHTML()
        : $this->_node->ownerDocument->saveHTML($this->_node);
    }
  }
}
