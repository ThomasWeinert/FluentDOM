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

namespace FluentDOM\Serializer {

  use FluentDOM\Utility\StringCastable;

  class Html implements StringCastable {
    /**
     * @var \DOMNode
     */
    protected $_node;

    /**
     * @param \DOMNode $node
     */
    public function __construct(\DOMNode $node) {
      $this->_node = $node;
    }

    /**
     * @return string
     */
    public function __toString(): string {
      return $this->_node instanceof \DOMDocument
        ? $this->_node->saveHTML()
        : $this->_node->ownerDocument->saveHTML($this->_node);
    }
  }
}
