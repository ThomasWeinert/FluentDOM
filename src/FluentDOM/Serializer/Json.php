<?php
/**
 * Abstract superclass for json serializers (serialize a DOM into a Json structure).
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer {

  /**
   * Abstract superclass for json serializers (serialize a DOM into a Json structure).
   *
   * See http://wiki.open311.org/index.php?title=JSON_and_XML_Conversion for
   * different formats.
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  abstract class Json implements \JsonSerializable {

    protected $_document = NULL;

    private $_options = 0;
    private $_depth = 512;

    public function __construct(\DOMDocument $document, $options = 0, $depth = 512) {
      $this->_document = $document;
      $this->_options = (int)$options;
      $this->_depth = (int)$depth;
    }

    public function __toString() {
      return json_encode($this, $this->_options, $this->_depth);
    }
  }
}